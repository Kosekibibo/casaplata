<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HalloweenController extends Controller
{
    public function index()
    {
        $config = DB::table('evento_config')->first();
        return view('halloween.index', compact('config'));
    }

    public function votar(Request $request)
    {
        $config = DB::table('evento_config')->first();
        $categoria = $request->query('categoria', 'mejor');

        $concursantes = DB::table('concursantes')
            ->where('categoria', $categoria)
            ->orderBy('id')
            ->get();

        $countMejor = DB::table('concursantes')->where('categoria', 'mejor')->count();
        $countPuti  = DB::table('concursantes')->where('categoria', 'puti')->count();

        $topCat = $request->query('top_cat', 'todas');
        $queryTop = DB::table('concursantes')->orderByDesc('votos');
        if (in_array($topCat, ['mejor', 'puti'])) {
            $queryTop->where('categoria', $topCat);
        }
        $top10Publico = $queryTop->limit(10)->get();

        return view('halloween.votar', compact('config', 'concursantes', 'categoria', 'countMejor', 'countPuti', 'top10Publico', 'topCat'));
    }

    public function registrarConcursante(Request $request)
    {
        $config = DB::table('evento_config')->first();
        if ((!$config || !$config->registro_activo) && !$request->session()->get('is_halloween_admin')) {
            return back()->with('error', 'El registro está cerrado.');
        }
        $data = $request->validate([
            'nombre_invitado' => 'required|string|max:100',
            'nombre_disfraz'  => 'required|string|max:140',
            'categoria'       => 'required|in:mejor,puti',
            'foto'            => 'nullable|string',
        ]);
        DB::table('concursantes')->insert([
            'nombre_invitado' => $data['nombre_invitado'],
            'nombre_disfraz'  => $data['nombre_disfraz'],
            'categoria'       => $data['categoria'],
            'foto'            => $data['foto'] ?? null,
            'votos'           => 0,
            'created_at'      => now(), 'updated_at' => now(),
        ]);
        return redirect()->route('halloween.votar', ['categoria' => $data['categoria']])->with('status', '¡Disfraz registrado!');
    }

    public function emitirVoto(Request $request, $id)
    {
        $config = DB::table('evento_config')->first();
        if (!$config || !$config->votaciones_activas || $config->resultados_finalizados) {
            return back()->with('error', 'Las votaciones están cerradas.');
        }
        $concursante = DB::table('concursantes')->where('id', $id)->first();
        if (!$concursante) return back();
        $hash = sha1($request->ip() . '|' . $request->session()->getId());
        $votoPrevio = DB::table('votos_emitidos')->where('dispositivo_hash', $hash)->where('categoria', $concursante->categoria)->first();
        DB::transaction(function () use ($votoPrevio, $concursante, $hash) {
            if ($votoPrevio) {
                DB::table('concursantes')->where('id', $votoPrevio->concursante_id)->decrement('votos');
                DB::table('votos_emitidos')->where('id', $votoPrevio->id)->update(['concursante_id' => $concursante->id, 'updated_at' => now()]);
            } else {
                DB::table('votos_emitidos')->insert(['dispositivo_hash' => $hash, 'categoria' => $concursante->categoria, 'concursante_id' => $concursante->id, 'created_at' => now(), 'updated_at' => now()]);
            }
            DB::table('concursantes')->where('id', $concursante->id)->increment('votos');
        });
        return back()->with('status', '¡Tu voto fue registrado!');
    }

    public function adminPanel(Request $request)
    {
        if (!$request->session()->get('is_halloween_admin')) return view('halloween.admin_login');
        $config = DB::table('evento_config')->first();
        $concursantes = DB::table('concursantes')->orderByDesc('votos')->get();
        $topCat = $request->query('top_cat', 'todas');
        $queryTop = DB::table('concursantes')->orderByDesc('votos');
        if (in_array($topCat, ['mejor', 'puti'])) $queryTop->where('categoria', $topCat);
        $top10 = $queryTop->limit(10)->get();
        return view('halloween.admin', compact('config', 'concursantes', 'top10', 'topCat'));
    }

    public function adminLogin(Request $request)
    {
        $u = env('ADMIN_HALLOWEEN_USER', 'admin_casaplata');
        $p = env('ADMIN_HALLOWEEN_PASS', 'CasaPlata2026!');
        if ($request->input('username') === $u && $request->input('password') === $p) {
            $request->session()->put('is_halloween_admin', true);
            return redirect()->route('halloween.admin');
        }
        return back()->with('error', 'Credenciales incorrectas');
    }

    public function toggleVotaciones(Request $request)
    {
        if (!$request->session()->get('is_halloween_admin')) abort(403);
        $c = DB::table('evento_config')->first();
        $nuevo = !$c->votaciones_activas;
        DB::table('evento_config')->where('id', $c->id)->update(['votaciones_activas' => $nuevo, 'resultados_finalizados' => $nuevo ? false : $c->resultados_finalizados]);
        return back();
    }

    public function toggleRegistro(Request $request)
    {
        if (!$request->session()->get('is_halloween_admin')) abort(403);
        $c = DB::table('evento_config')->first();
        DB::table('evento_config')->where('id', $c->id)->update(['registro_activo' => !$c->registro_activo]);
        return back();
    }

    public function finalizarResultados(Request $request)
    {
        if (!$request->session()->get('is_halloween_admin')) abort(403);
        $c = DB::table('evento_config')->first();
        DB::table('evento_config')->where('id', $c->id)->update(['votaciones_activas' => false, 'resultados_finalizados' => true]);
        return back()->with('status', '¡Resultado Final Top 1 al 10 publicado para todos!');
    }

    public function reabrirVotaciones(Request $request)
    {
        if (!$request->session()->get('is_halloween_admin')) abort(403);
        $c = DB::table('evento_config')->first();
        DB::table('evento_config')->where('id', $c->id)->update(['votaciones_activas' => true, 'resultados_finalizados' => false]);
        return back()->with('status', 'Votaciones reabiertas.');
    }

    public function actualizarConcursante(Request $request, $id)
    {
        if (!$request->session()->get('is_halloween_admin')) abort(403);
        $data = $request->validate([
            'nombre_invitado' => 'required|string|max:100',
            'nombre_disfraz'  => 'required|string|max:140',
            'categoria'       => 'required|in:mejor,puti',
            'foto'            => 'nullable|string',
        ]);
        $upd = ['nombre_invitado' => $data['nombre_invitado'], 'nombre_disfraz' => $data['nombre_disfraz'], 'categoria' => $data['categoria'], 'updated_at' => now()];
        if (!empty($data['foto'])) $upd['foto'] = $data['foto'];
        DB::table('concursantes')->where('id', $id)->update($upd);
        return back()->with('status', 'Concursante actualizado.');
    }

    public function eliminarConcursante(Request $request, $id)
    {
        if (!$request->session()->get('is_halloween_admin')) abort(403);
        DB::table('concursantes')->where('id', $id)->delete();
        return back();
    }

    public function reiniciarVotos(Request $request)
    {
        if (!$request->session()->get('is_halloween_admin')) abort(403);
        DB::table('votos_emitidos')->truncate();
        DB::table('concursantes')->update(['votos' => 0]);
        return back();
    }

    public function adminLogout(Request $request)
    {
        $request->session()->forget('is_halloween_admin');
        return redirect()->route('halloween.admin');
    }
}
