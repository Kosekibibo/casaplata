<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Halloween 7ta Edición - Casa Plata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700;900&family=Creepster&family=Montserrat:wght@400;600;700;800;900&family=Rye&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --blood-red: #dc2626; --halloween-orange: #f97316; }
        body { font-family: "Montserrat", sans-serif; background-color: #070506; color: #f4f4f5; overflow-x: hidden; }
        .font-rye { font-family: "Rye", serif; }
        .font-cinzel { font-family: "Cinzel Decorative", serif; }
        .font-creepster { font-family: "Creepster", cursive; }
        .hazard-tape {
            background: repeating-linear-gradient(-45deg, #eab308, #eab308 22px, #09090b 22px, #09090b 44px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.85), inset 0 1px 2px rgba(255, 255, 255, 0.3);
            border-top: 2px solid #ca8a04; border-bottom: 2px solid #ca8a04;
        }
        .glow-orange { text-shadow: 0 0 12px rgba(249, 115, 22, 0.75), 0 0 30px rgba(234, 88, 12, 0.45); }
        .glow-silver { text-shadow: 0 0 15px rgba(255, 255, 255, 0.9), 0 0 30px rgba(228, 228, 231, 0.5); }
        .glow-blood { text-shadow: 0 4px 0 #450a0a, 0 0 20px rgba(220, 38, 38, 0.9), 0 0 40px rgba(185, 28, 28, 0.6); }
        .metal-plate {
            background: linear-gradient(160deg, rgba(24, 15, 17, 0.96) 0%, rgba(9, 6, 7, 0.99) 100%);
            border: 1.5px solid #7f1d1d;
            box-shadow: 0 0 30px rgba(153, 27, 27, 0.35), inset 0 0 25px rgba(0, 0, 0, 0.9);
        }
        .rivet {
            width: 9px; height: 9px; border-radius: 9999px;
            background: radial-gradient(circle at 30% 30%, #f87171, #7f1d1d 70%, #270909 100%);
            box-shadow: 0 1px 3px rgba(0,0,0,0.9), 0 0 6px rgba(239, 68, 68, 0.6);
        }
        .btn-blood-3d {
            background: linear-gradient(180deg, #b91c1c 0%, #7f1d1d 100%);
            border: 1px solid #ef4444;
            box-shadow: 0 6px 0 #450a0a, 0 12px 25px rgba(220, 38, 38, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.3);
            transition: all 0.15s ease;
        }
        .btn-blood-3d:hover { background: linear-gradient(180deg, #dc2626 0%, #991b1b 100%); transform: translateY(-2px); }
        .btn-blood-3d:active { transform: translateY(4px); box-shadow: 0 2px 0 #450a0a; }
        @keyframes floatSlow { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-5px); } }
        .animate-float { animation: floatSlow 4s ease-in-out infinite; }
        .status-dot-green {
            width: 9px; height: 9px; border-radius: 9999px; background-color: #10b981;
            box-shadow: 0 0 10px #10b981, 0 0 18px #34d399; position: relative; display: inline-block;
        }
        .status-dot-green::after {
            content: ""; position: absolute; inset: -4px; border-radius: 9999px;
            border: 2px solid #34d399; animation: pulseRing 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        .status-dot-amber {
            width: 9px; height: 9px; border-radius: 9999px; background-color: #f59e0b;
            box-shadow: 0 0 10px #f59e0b, 0 0 18px #fbbf24; position: relative; display: inline-block;
        }
        .status-dot-amber::after {
            content: ""; position: absolute; inset: -4px; border-radius: 9999px;
            border: 2px solid #fbbf24; animation: pulseRing 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        @keyframes pulseRing { 0% { transform: scale(0.8); opacity: 1; } 100% { transform: scale(2); opacity: 0; } }
    </style>
</head>
<body class="min-h-[100dvh] relative selection:bg-red-600 selection:text-white">
    <canvas id="emberCanvas" class="fixed inset-0 pointer-events-none z-0"></canvas>
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[500px] h-[320px] rounded-full bg-red-950/30 blur-[110px]"></div>
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[450px] h-[280px] rounded-full bg-orange-950/20 blur-[110px]"></div>
    </div>
    <script>
        window.addEventListener("load", function () {
            const canvas = document.getElementById("emberCanvas");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");
            let particles = [];
            function resizeCanvas() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();
            function createParticle() {
                const colors = ["#ef4444", "#f97316", "#eab308", "#991b1b"];
                return {
                    x: Math.random() * canvas.width,
                    y: canvas.height + Math.random() * 100,
                    size: Math.random() * 2.8 + 0.8,
                    speedY: Math.random() * 0.9 + 0.3,
                    speedX: (Math.random() - 0.5) * 0.6,
                    alpha: Math.random() * 0.65 + 0.2,
                    color: colors[Math.floor(Math.random() * colors.length)]
                };
            }
            for (let i = 0; i < 45; i++) {
                const p = createParticle(); p.y = Math.random() * canvas.height; particles.push(p);
            }
            function animateParticles() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                for (let i = 0; i < particles.length; i++) {
                    const p = particles[i];
                    p.y -= p.speedY; p.x += p.speedX;
                    ctx.save(); ctx.globalAlpha = p.alpha; ctx.fillStyle = p.color;
                    ctx.shadowBlur = 8; ctx.shadowColor = p.color;
                    ctx.beginPath(); ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2); ctx.fill(); ctx.restore();
                    if (p.y < -10) particles[i] = createParticle();
                }
                requestAnimationFrame(animateParticles);
            }
            animateParticles();
        });
    </script>
    <main class="relative z-10 min-h-[100dvh] flex flex-col justify-center sm:py-6">
        <div class="w-full sm:max-w-[430px] mx-auto min-h-[100dvh] sm:min-h-[760px] bg-gradient-to-b from-[#0e0708] via-[#090506] to-[#050304] sm:rounded-[2rem] sm:border border-red-950/80 shadow-[0_0_70px_rgba(0,0,0,0.95)] relative overflow-hidden flex flex-col justify-between py-6">
            <!-- Cinta de peligro superior -->
            <div class="relative h-14 overflow-hidden pointer-events-none select-none">
                <div class="hazard-tape w-[130%] h-8 -ml-[15%] mt-2 transform -rotate-3 flex items-center justify-around">
                    <span class="text-[9px] font-black tracking-[0.35em] text-black uppercase bg-yellow-400/95 px-2 py-0.5 rounded-sm">NO PASAR • ZONA CASA PLATA</span>
                    <span class="text-[9px] font-black tracking-[0.35em] text-black uppercase bg-yellow-400/95 px-2 py-0.5 rounded-sm">HALLOWEEN VII</span>
                </div>
            </div>

            <!-- Contenido central del póster -->
            <div class="px-5 sm:px-8 flex flex-col items-center text-center relative z-10 my-auto space-y-5">
                <?php if($config && $config->resultados_finalizados): ?>
                    <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-amber-950/90 border border-amber-500/80 text-[11px] font-bold tracking-widest text-amber-300 uppercase shadow-[0_0_25px_rgba(245,158,11,0.3)]">
                        <span>👑</span><span>Ganadores Oficiales</span>
                    </div>
                <?php elseif($config && $config->votaciones_activas): ?>
                    <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-emerald-950/80 border border-emerald-600/60 text-[11px] font-bold tracking-widest text-emerald-400 uppercase shadow-[0_0_20px_rgba(16,185,129,0.2)]">
                        <span class="status-dot-green"></span><span>Votaciones Abiertas</span>
                    </div>
                <?php else: ?>
                    <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-amber-950/80 border border-amber-600/60 text-[11px] font-bold tracking-widest text-amber-400 uppercase">
                        <span class="status-dot-amber"></span><span>Concurso por Iniciar</span>
                    </div>
                <?php endif; ?>

                <div class="space-y-1">
                    <h1 class="font-rye text-4xl sm:text-[2.7rem] tracking-[0.12em] text-orange-500 glow-orange uppercase leading-none">HALLOWEEN</h1>
                    <p class="font-montserrat text-xs sm:text-sm font-bold tracking-[0.42em] text-zinc-400 uppercase pt-1">7TA EDICIÓN</p>
                </div>

                <div class="pt-1">
                    <h2 class="font-cinzel font-black text-3xl sm:text-4xl tracking-[0.1em] text-white glow-silver uppercase">CASA PLATA</h2>
                </div>

                <div class="pb-1">
                    <p class="font-creepster text-5xl sm:text-6xl tracking-[0.08em] text-red-600 glow-blood uppercase leading-none animate-float">HASTA MORIR</p>
                </div>

                <!-- Placa Metálica con Remaches -->
                <div class="metal-plate w-full rounded-2xl p-5 relative">
                    <div class="rivet absolute top-3 left-3"></div>
                    <div class="rivet absolute top-3 right-3"></div>
                    <div class="rivet absolute bottom-3 left-3"></div>
                    <div class="rivet absolute bottom-3 right-3"></div>
                    <p class="font-montserrat text-xs sm:text-sm font-extrabold tracking-[0.22em] text-zinc-300 uppercase mb-4">CONCURSO DE DISFRACES</p>
                    <div class="space-y-3">
                        <a href="<?php echo e(route('halloween.votar', ['categoria' => 'mejor'])); ?>" class="w-full py-2.5 px-4 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 transition flex items-center justify-center gap-2.5">
                            <span class="text-lg">🏆</span>
                            <span class="font-montserrat font-extrabold text-sm sm:text-base tracking-[0.14em] text-amber-400 uppercase">MEJOR DISFRAZ</span>
                        </a>
                        <a href="<?php echo e(route('halloween.votar', ['categoria' => 'puti'])); ?>" class="w-full py-2.5 px-4 rounded-xl bg-red-600/10 hover:bg-red-600/20 border border-red-500/30 transition flex items-center justify-center gap-2">
                            <span class="text-lg">🔥</span>
                            <span class="font-montserrat font-extrabold text-sm sm:text-base tracking-[0.12em] text-red-500 uppercase">MEJOR PUTI DISFRAZ</span>
                            <span class="text-lg">🔥</span>
                        </a>
                    </div>
                </div>

                <!-- Botón Principal 3D -->
                <div class="w-full pt-1">
                    <a href="<?php echo e(route('halloween.votar')); ?>" class="btn-blood-3d w-full py-4 px-6 rounded-xl font-montserrat font-extrabold text-base sm:text-lg tracking-[0.22em] text-white uppercase flex items-center justify-center gap-3">
                        <span><?php echo e(($config && $config->resultados_finalizados) ? '🏆 VER GANADORES (TOP 10)' : 'ENTRAR A VOTAR'); ?></span>
                        <i class="fa-solid fa-arrow-right text-sm text-red-300"></i>
                    </a>
                </div>

                <?php if($config && $config->registro_activo && !$config->resultados_finalizados): ?>
                    <div class="flex flex-wrap items-center justify-center gap-3 text-xs text-zinc-400 pt-1">
                        <a href="<?php echo e(route('halloween.votar', ['inscribir' => 1])); ?>" class="text-orange-400 hover:text-orange-300 underline underline-offset-4 font-bold">
                            Registrar mi disfraz
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cinta de peligro inferior -->
            <div class="relative h-14 overflow-hidden pointer-events-none select-none">
                <div class="hazard-tape w-[130%] h-8 -ml-[15%] mt-3 transform rotate-2 flex items-center justify-around">
                    <span class="text-[9px] font-black tracking-[0.35em] text-black uppercase bg-yellow-400/95 px-2 py-0.5 rounded-sm">PELIGRO • HASTA MORIR</span>
                    <span class="text-[9px] font-black tracking-[0.35em] text-black uppercase bg-yellow-400/95 px-2 py-0.5 rounded-sm">CASA PLATA</span>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
<?php /**PATH /home/youcaffe/casaplata/resources/views/halloween/index.blade.php ENDPATH**/ ?>