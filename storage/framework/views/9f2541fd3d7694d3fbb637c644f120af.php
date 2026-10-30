<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Organizador - Casa Plata</title>
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
<body class="min-h-[100dvh] p-4 sm:p-6 relative">
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
    <div id="global-lightbox" onclick="closeLightbox()" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/90 backdrop-blur-md">
        <div onclick="event.stopPropagation()" class="relative max-w-md w-full bg-zinc-950 border-2 border-red-800/80 rounded-3xl overflow-hidden shadow-[0_0_60px_rgba(220,38,38,0.35)]">
            <button type="button" onclick="closeLightbox()" class="absolute top-3 right-3 z-10 w-10 h-10 rounded-full bg-black/80 text-white hover:bg-red-600 border border-white/20 flex items-center justify-center text-base shadow-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="w-full aspect-square bg-black flex items-center justify-center overflow-hidden">
                <img id="global-lightbox-img" src="" alt="Foto Disfraz" class="w-full h-full object-contain">
            </div>
            <div class="p-4 text-center bg-zinc-950 border-t border-zinc-800">
                <h4 id="global-lightbox-title" class="font-cinzel font-black text-lg sm:text-xl text-white"></h4>
                <p id="global-lightbox-sub" class="text-xs sm:text-sm text-orange-400 font-bold mt-0.5"></p>
            </div>
        </div>
    </div>
    <script>
        function openLightbox(src, title, sub) {
            const m = document.getElementById("global-lightbox");
            document.getElementById("global-lightbox-img").src = src || "https://placehold.co/600x600/18181b/f97316?text=Disfraz";
            document.getElementById("global-lightbox-title").textContent = title || "";
            document.getElementById("global-lightbox-sub").textContent = sub ? ("Por: " + sub) : "";
            m.classList.remove("hidden"); m.classList.add("flex");
        }
        function closeLightbox() {
            const m = document.getElementById("global-lightbox");
            m.classList.add("hidden"); m.classList.remove("flex");
        }
        let activeStream = null;
        const cropInstances = {};
        function getCropInst(prefix) {
            if (!cropInstances[prefix]) {
                cropInstances[prefix] = { img: null, scale: 1, minScale: 1, x: 0, y: 0, dragging: false, sx: 0, sy: 0, bound: false };
            }
            return cropInstances[prefix];
        }
        function bindCropCanvas(prefix) {
            const st = getCropInst(prefix);
            if (st.bound) return;
            const c = document.getElementById(prefix + "-crop-canvas");
            if (!c) return;
            st.bound = true;
            function pt(e) { return (e.touches && e.touches[0]) ? { x: e.touches[0].clientX, y: e.touches[0].clientY } : { x: e.clientX, y: e.clientY }; }
            function start(e) { if (!st.img) return; st.dragging = true; const p = pt(e); st.sx = p.x; st.sy = p.y; }
            function move(e) {
                if (!st.dragging || !st.img) return;
                if (e.cancelable) e.preventDefault();
                const p = pt(e); const r = c.getBoundingClientRect(); const ratio = c.width / (r.width || 200);
                st.x += (p.x - st.sx) * ratio; st.y += (p.y - st.sy) * ratio;
                st.sx = p.x; st.sy = p.y;
                drawCrop(prefix);
            }
            function end() { st.dragging = false; }
            c.addEventListener("mousedown", start); window.addEventListener("mousemove", move); window.addEventListener("mouseup", end);
            c.addEventListener("touchstart", start, { passive: true }); c.addEventListener("touchmove", move, { passive: false }); c.addEventListener("touchend", end);
        }
        function drawCrop(prefix) {
            const st = getCropInst(prefix);
            const c = document.getElementById(prefix + "-crop-canvas");
            if (!c || !st.img) return;
            const ctx = c.getContext("2d");
            const sz = 700;
            const dw = st.img.width * st.scale, dh = st.img.height * st.scale;
            st.x = dw >= sz ? Math.min(0, Math.max(sz - dw, st.x)) : (sz - dw) / 2;
            st.y = dh >= sz ? Math.min(0, Math.max(sz - dh, st.y)) : (sz - dh) / 2;
            ctx.fillStyle = "#09090b"; ctx.fillRect(0, 0, sz, sz);
            ctx.drawImage(st.img, st.x, st.y, dw, dh);
        }
        function openCropEditor(prefix, dataUrl) {
            bindCropCanvas(prefix);
            const st = getCropInst(prefix);
            const im = new Image();
            im.onload = function() {
                st.img = im;
                const sz = 700;
                const cover = Math.max(sz / im.width, sz / im.height);
                st.minScale = cover; st.scale = cover;
                st.x = (sz - im.width * cover) / 2; st.y = (sz - im.height * cover) / 2;
                const z = document.getElementById(prefix + "-crop-zoom"); if (z) z.value = 1;
                document.getElementById(prefix + "-prompt").classList.add("hidden");
                document.getElementById(prefix + "-live").classList.add("hidden");
                document.getElementById(prefix + "-preview").classList.add("hidden");
                document.getElementById(prefix + "-cropper").classList.remove("hidden");
                drawCrop(prefix);
            };
            im.src = dataUrl;
        }
        function zoomCrop(prefix, mult) {
            const st = getCropInst(prefix); if (!st.img) return;
            const sz = 700; const prev = st.scale; const next = st.minScale * parseFloat(mult);
            const cx = (sz / 2 - st.x) / prev, cy = (sz / 2 - st.y) / prev;
            st.scale = next; st.x = sz / 2 - cx * next; st.y = sz / 2 - cy * next;
            drawCrop(prefix);
        }
        function confirmCrop(prefix, hiddenInputId) {
            const c = document.getElementById(prefix + "-crop-canvas"); if (!c) return;
            const out = c.toDataURL("image/jpeg", 0.82);
            document.getElementById(hiddenInputId).value = out;
            document.getElementById(prefix + "-img").src = out;
            document.getElementById(prefix + "-cropper").classList.add("hidden");
            document.getElementById(prefix + "-preview").classList.remove("hidden");
        }
        function reopenCrop(prefix, hiddenInputId) {
            const st = getCropInst(prefix);
            if (st.img) {
                document.getElementById(prefix + "-preview").classList.add("hidden");
                document.getElementById(prefix + "-cropper").classList.remove("hidden");
                drawCrop(prefix);
            } else {
                const val = document.getElementById(hiddenInputId).value;
                if (val) openCropEditor(prefix, val);
            }
        }
        function initCam(prefix) {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) return;
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user", width: { ideal: 720 }, height: { ideal: 720 } }, audio: false })
                .then(function(s) {
                    activeStream = s;
                    const v = document.getElementById(prefix + "-video");
                    v.srcObject = s;
                    document.getElementById(prefix + "-prompt").classList.add("hidden");
                    document.getElementById(prefix + "-preview").classList.add("hidden");
                    document.getElementById(prefix + "-cropper").classList.add("hidden");
                    document.getElementById(prefix + "-live").classList.remove("hidden");
                }).catch(function() {});
        }
        function stopCam(prefix, hiddenInputId) {
            if (activeStream) { activeStream.getTracks().forEach(function(t){ t.stop(); }); activeStream = null; }
            const lb = document.getElementById(prefix + "-live"); if (lb) lb.classList.add("hidden");
            const val = document.getElementById(hiddenInputId).value;
            if (!val && document.getElementById(prefix + "-cropper").classList.contains("hidden")) {
                document.getElementById(prefix + "-prompt").classList.remove("hidden");
            }
        }
        function snapPhoto(prefix) {
            const v = document.getElementById(prefix + "-video");
            const c = document.createElement("canvas");
            c.width = v.videoWidth || 640; c.height = v.videoHeight || 640;
            c.getContext("2d").drawImage(v, 0, 0, c.width, c.height);
            const dataUrl = c.toDataURL("image/jpeg", 0.9);
            if (activeStream) { activeStream.getTracks().forEach(function(t){ t.stop(); }); activeStream = null; }
            openCropEditor(prefix, dataUrl);
        }
        function handleFileImg(ev, prefix) {
            const f = ev.target.files && ev.target.files[0]; if (!f) return;
            const r = new FileReader();
            r.onload = function(e) { openCropEditor(prefix, e.target.result); };
            r.readAsDataURL(f);
        }
        function resetCamUI(prefix, hiddenInputId) {
            if (activeStream) { activeStream.getTracks().forEach(function(t){ t.stop(); }); activeStream = null; }
            document.getElementById(hiddenInputId).value = "";
            const fi = document.getElementById(prefix + "-file"); if (fi) fi.value = "";
            document.getElementById(prefix + "-preview").classList.add("hidden");
            document.getElementById(prefix + "-live").classList.add("hidden");
            document.getElementById(prefix + "-cropper").classList.add("hidden");
            document.getElementById(prefix + "-prompt").classList.remove("hidden");
        }
        function ensureCroppedBeforeSubmit(prefix, hiddenInputId) {
            const cropBox = document.getElementById(prefix + "-cropper");
            if (cropBox && !cropBox.classList.contains("hidden")) {
                confirmCrop(prefix, hiddenInputId);
            }
        }
    </script>
    <div class="relative z-10 max-w-4xl mx-auto space-y-5">
        <div class="bg-zinc-950/95 border border-red-900/60 rounded-2xl p-4 sm:p-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 block">● Conectado a PostgreSQL (casaplata_db)</span>
                <h1 class="font-cinzel font-black text-xl text-white">Panel de Control • Casa Plata</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="openAdminNewModal()" class="px-3 py-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-xs font-bold text-white">
                    <i class="fa-solid fa-camera mr-1"></i> + Dar de Alta Disfraz
                </button>
                <a href="<?php echo e(route('halloween.votar')); ?>" class="px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs font-bold text-zinc-200">Ver Votación</a>
                <form method="POST" action="<?php echo e(route('halloween.admin.logout')); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-800 text-xs font-bold text-zinc-400 hover:text-white">Salir</button>
                </form>
            </div>
        </div>

        <?php if(session('status')): ?>
            <div class="p-3.5 rounded-xl bg-emerald-950/90 border border-emerald-600 text-xs font-bold text-emerald-300"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        <!-- 3 INTERRUPTORES MAESTROS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-4 flex items-center justify-between gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase text-zinc-400 block">Estado de Votación</span>
                    <h3 class="text-base font-extrabold <?php echo e($config->votaciones_activas ? 'text-emerald-400' : 'text-amber-400'); ?> mt-0.5"><?php echo e($config->votaciones_activas ? '🟢 ABIERTAS' : '🔴 PAUSADAS'); ?></h3>
                </div>
                <form method="POST" action="<?php echo e(route('halloween.admin.toggleVotaciones')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-3.5 py-2.5 rounded-xl font-extrabold text-xs uppercase <?php echo e($config->votaciones_activas ? 'bg-red-950 text-red-300 border border-red-700' : 'bg-emerald-600 text-white shadow'); ?>">
                        <?php echo e($config->votaciones_activas ? 'Pausar Votos' : 'Activar Votos'); ?>

                    </button>
                </form>
            </div>
            <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-4 flex items-center justify-between gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase text-zinc-400 block">Registro Invitados</span>
                    <h3 class="text-base font-extrabold <?php echo e($config->registro_activo ? 'text-emerald-400' : 'text-red-400'); ?> mt-0.5"><?php echo e($config->registro_activo ? '🟢 ABIERTO' : '🔴 CERRADO'); ?></h3>
                </div>
                <form method="POST" action="<?php echo e(route('halloween.admin.toggleRegistro')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-3.5 py-2.5 rounded-xl font-extrabold text-xs uppercase bg-zinc-900 text-zinc-200 border border-zinc-700">
                        <?php echo e($config->registro_activo ? 'Cerrar Registro' : 'Abrir Registro'); ?>

                    </button>
                </form>
            </div>
            <div class="bg-zinc-950 border border-amber-600/50 rounded-2xl p-4 flex items-center justify-between gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase text-amber-400 block">Podio Oficial (Público)</span>
                    <h3 class="text-base font-extrabold <?php echo e($config->resultados_finalizados ? 'text-amber-400' : 'text-zinc-200'); ?> mt-0.5"><?php echo e($config->resultados_finalizados ? '👑 PUBLICADO' : '🏆 TOP 1 AL 10'); ?></h3>
                </div>
                <div class="flex flex-col gap-1.5">
                    <form method="POST" action="<?php echo e(route('halloween.admin.finalizar')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full px-3.5 py-2 rounded-xl font-extrabold text-xs uppercase bg-amber-500 hover:bg-amber-400 text-black shadow"><i class="fa-solid fa-crown mr-1"></i> Resultado Final</button>
                    </form>
                    <?php if($config->resultados_finalizados): ?>
                        <form method="POST" action="<?php echo e(route('halloween.admin.reabrir')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full px-3 py-1.5 rounded-xl font-bold text-[11px] uppercase bg-red-950 text-red-300 border border-red-700"><i class="fa-solid fa-rotate-left mr-1"></i> Reabrir Votación</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- TABLA OFICIAL TOP 1 AL 10 -->
        <?php if($config->resultados_finalizados): ?>
            <div class="bg-gradient-to-b from-amber-950/30 via-zinc-950 to-zinc-950 border-2 border-amber-500/70 rounded-3xl overflow-hidden shadow-2xl">
                <div class="p-4 sm:p-5 border-b border-amber-500/30 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-400 block">Votaciones Cerradas • Visible en celulares de invitados</span>
                        <h3 class="font-cinzel font-black text-lg sm:text-xl text-white">Tabla Oficial: Top 1 al 10</h3>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="<?php echo e(route('halloween.admin', ['top_cat' => 'todas'])); ?>" class="px-3 py-1.5 rounded-xl text-xs font-extrabold <?php echo e(($topCat ?? 'todas') === 'todas' ? 'bg-amber-500 text-black' : 'bg-zinc-900 text-zinc-300 border border-zinc-700'); ?>">General (Top 10)</a>
                        <a href="<?php echo e(route('halloween.admin', ['top_cat' => 'mejor'])); ?>" class="px-3 py-1.5 rounded-xl text-xs font-bold <?php echo e(($topCat ?? 'todas') === 'mejor' ? 'bg-amber-500 text-black' : 'bg-zinc-900 text-zinc-300 border border-zinc-700'); ?>">🏆 Mejor Disfraz</a>
                        <a href="<?php echo e(route('halloween.admin', ['top_cat' => 'puti'])); ?>" class="px-3 py-1.5 rounded-xl text-xs font-bold <?php echo e(($topCat ?? 'todas') === 'puti' ? 'bg-amber-500 text-black' : 'bg-zinc-900 text-zinc-300 border border-zinc-700'); ?>">🔥 Puti Disfraz</a>
                        <form method="POST" action="<?php echo e(route('halloween.admin.reabrir')); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-extrabold bg-red-600 text-white"><i class="fa-solid fa-rotate-left mr-1"></i> Me equivoqué: Reabrir Votación</button>
                        </form>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-amber-500/20 text-[10px] font-extrabold uppercase text-amber-300/80 bg-amber-950/20">
                                <th class="py-3 px-4">Puesto</th><th class="py-3 px-4">Participante / Disfraz</th><th class="py-3 px-4">Categoría</th><th class="py-3 px-4 text-right">Total Votos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/80 text-xs sm:text-sm">
                            <?php $__empty_1 = true; $__currentLoopData = $top10; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $rk = $idx + 1; $fotoAdmTop = $t->foto ?: "https://placehold.co/600x600/18181b/f97316?text=Foto"; ?>
                                <tr class="<?php echo e($rk === 1 ? 'bg-amber-500/10' : ''); ?>">
                                    <td class="py-3 px-4 font-black text-amber-400"><?php echo e($rk === 1 ? '🥇 1° LUGAR' : ($rk === 2 ? '🥈 2° LUGAR' : ($rk === 3 ? '🥉 3° LUGAR' : '#'.$rk))); ?></td>
                                    <td class="py-3 px-4 flex items-center gap-3">
                                        <img onclick="openLightbox(<?php echo e(json_encode($fotoAdmTop)); ?>, <?php echo e(json_encode($t->nombre_disfraz)); ?>, <?php echo e(json_encode($t->nombre_invitado)); ?>)" src="<?php echo e($fotoAdmTop); ?>" class="w-14 h-14 rounded-xl object-cover border border-amber-500/40 cursor-pointer hover:scale-105 transition">
                                        <div><div class="font-extrabold text-white"><?php echo e($t->nombre_disfraz); ?></div><div class="text-xs text-zinc-400"><?php echo e($t->nombre_invitado); ?></div></div>
                                    </td>
                                    <td class="py-3 px-4 text-xs font-bold text-zinc-300"><?php echo e($t->categoria === 'mejor' ? '🏆 Mejor Disfraz' : '🔥 Puti Disfraz'); ?></td>
                                    <td class="py-3 px-4 text-right font-black text-lg text-amber-400"><?php echo e($t->votos); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="py-6 text-center text-zinc-500">Sin concursantes registrados.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- TABLA GENERAL DE CONCURSANTES -->
        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl overflow-hidden">
            <div class="p-4 border-b border-zinc-800 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="font-bold text-sm text-white">Tabla concursantes en PostgreSQL</h3>
                    <p class="text-[11px] text-zinc-400">Pulsa <strong class="text-amber-400">Editar / Foto</strong> para corregir datos o toca cualquier foto para verla en grande.</p>
                </div>
                <form method="POST" action="<?php echo e(route('halloween.admin.reiniciar')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-xs font-bold text-amber-400 px-3 py-1.5 rounded-lg bg-amber-950/40 border border-amber-800/40"><i class="fa-solid fa-rotate-left mr-1"></i> Reiniciar Votos a 0</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-800 text-[10px] font-extrabold uppercase text-zinc-400 bg-zinc-900/50">
                            <th class="py-2.5 px-4">Foto / Disfraz</th><th class="py-2.5 px-4">Categoría</th><th class="py-2.5 px-4">Votos</th><th class="py-2.5 px-4 text-right">Acciones Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/70 text-xs">
                        <?php $__empty_1 = true; $__currentLoopData = $concursantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $fotoAdm = $c->foto ?: "https://placehold.co/600x600/18181b/f97316?text=Foto"; ?>
                            <tr class="hover:bg-zinc-900/40">
                                <td class="py-2.5 px-4 flex items-center gap-2.5">
                                    <img onclick="openLightbox(<?php echo e(json_encode($fotoAdm)); ?>, <?php echo e(json_encode($c->nombre_disfraz)); ?>, <?php echo e(json_encode($c->nombre_invitado)); ?>)" src="<?php echo e($fotoAdm); ?>" class="w-14 h-14 rounded-xl object-cover border border-zinc-700 cursor-pointer hover:scale-105 transition">
                                    <div><div class="font-bold text-white"><?php echo e($c->nombre_disfraz); ?></div><div class="text-zinc-400 text-[11px]"><?php echo e($c->nombre_invitado); ?></div></div>
                                </td>
                                <td class="py-2.5 px-4"><?php echo $c->categoria === 'mejor' ? '<span class="px-2 py-0.5 rounded bg-amber-500/15 text-amber-300 font-bold">🏆 Mejor</span>' : '<span class="px-2 py-0.5 rounded bg-red-500/15 text-red-300 font-bold">🔥 Puti</span>'; ?></td>
                                <td class="py-2.5 px-4 font-extrabold text-orange-400"><?php echo e($c->votos); ?></td>
                                <td class="py-2.5 px-4 text-right space-x-1 whitespace-nowrap">
                                    <button type="button" onclick="openAdminEditModal(<?php echo e($c->id); ?>, <?php echo e(json_encode($c->nombre_invitado)); ?>, <?php echo e(json_encode($c->nombre_disfraz)); ?>, <?php echo e(json_encode($c->categoria)); ?>, <?php echo e(json_encode($c->foto)); ?>)" class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 font-bold"><i class="fa-solid fa-pen-to-square mr-1"></i> Editar / Foto</button>
                                    <form method="POST" action="<?php echo e(route('halloween.admin.eliminar', $c->id)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-950/60 text-red-300 font-bold"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="py-6 text-center text-zinc-500">No hay concursantes registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL ADMIN PARA CREAR O EDITAR CONCURSANTE Y REENCUADRAR FOTO -->
    <div id="admin-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/85 backdrop-blur-sm overflow-y-auto">
        <div class="bg-zinc-950 border border-red-900/70 rounded-2xl max-w-md w-full p-5 sm:p-6 relative my-auto">
            <button type="button" onclick="closeAdminModal()" class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-zinc-900 text-zinc-400 hover:text-white flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
            <h3 id="adm-modal-title" class="font-cinzel font-black text-xl text-white uppercase">Editar Participante</h3>
            <form id="adm-form" method="POST" action="<?php echo e(route('halloween.registrar')); ?>" onsubmit="ensureCroppedBeforeSubmit('admcam', 'adm-foto-input')" class="space-y-4 mt-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="foto" id="adm-foto-input" value="">
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-300 mb-1">Nombre del Invitado</label>
                    <input type="text" name="nombre_invitado" id="adm-nombre" required class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-300 mb-1">Nombre del Disfraz</label>
                    <input type="text" name="nombre_disfraz" id="adm-disfraz" required class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-300 mb-1">Categoría</label>
                    <select name="categoria" id="adm-cat" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-sm text-white">
                        <option value="mejor">🏆 MEJOR DISFRAZ</option>
                        <option value="puti">🔥 MEJOR PUTI DISFRAZ 🔥</option>
                    </select>
                </div>
                <div class="bg-zinc-900/90 border border-zinc-800 rounded-2xl p-3.5 space-y-3">
                    <label class="text-xs font-bold uppercase text-orange-400 flex items-center gap-1.5"><i class="fa-solid fa-camera"></i> Fotografía</label>
                    <div id="admcam-prompt" class="bg-zinc-950 border border-amber-500/40 rounded-xl p-3 text-center space-y-2">
                        <div class="flex flex-wrap justify-center gap-2">
                            <button type="button" onclick="initCam('admcam')" class="px-3 py-2 rounded-xl bg-emerald-600 text-white font-extrabold text-xs"><i class="fa-solid fa-video mr-1"></i> Encender Cámara</button>
                            <label class="px-3 py-2 rounded-xl bg-zinc-800 text-zinc-200 font-bold text-xs cursor-pointer border border-zinc-700">
                                <i class="fa-solid fa-images text-orange-400 mr-1"></i> Elegir de Galería
                                <input type="file" id="admcam-file" accept="image/*" onchange="handleFileImg(event, 'admcam')" class="hidden">
                            </label>
                        </div>
                    </div>
                    <div id="admcam-live" class="hidden space-y-2 text-center">
                        <div class="relative rounded-xl overflow-hidden bg-black aspect-square max-h-52 mx-auto"><video id="admcam-video" autoplay playsinline muted class="w-full h-full object-cover"></video></div>
                        <div class="flex justify-center gap-2">
                            <button type="button" onclick="snapPhoto('admcam')" class="btn-blood-3d px-4 py-2 rounded-xl font-extrabold text-xs text-white uppercase">Capturar</button>
                            <button type="button" onclick="stopCam('admcam', 'adm-foto-input')" class="px-3 py-2 rounded-xl bg-zinc-800 text-zinc-300 text-xs font-bold">Cancelar</button>
                        </div>
                    </div>
                    <div id="admcam-cropper" class="hidden space-y-2 text-center bg-zinc-950 p-3 rounded-xl border border-amber-500/50">
                        <p class="text-[11px] font-bold text-amber-300">Arrastra la foto para acomodarla</p>
                        <div class="relative w-48 h-48 mx-auto rounded-2xl overflow-hidden border-2 border-amber-500 bg-black touch-none select-none cursor-move">
                            <canvas id="admcam-crop-canvas" width="700" height="700" class="w-full h-full block"></canvas>
                        </div>
                        <input type="range" id="admcam-crop-zoom" min="1" max="3" step="0.02" value="1" oninput="zoomCrop('admcam', this.value)" class="w-full accent-amber-500">
                        <div class="flex justify-center gap-2">
                            <button type="button" onclick="confirmCrop('admcam', 'adm-foto-input')" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white font-extrabold text-xs uppercase">Confirmar Encuadre</button>
                            <button type="button" onclick="resetCamUI('admcam', 'adm-foto-input')" class="px-3 py-1.5 rounded-xl bg-zinc-800 text-zinc-300 text-xs font-bold">Cambiar</button>
                        </div>
                    </div>
                    <div id="admcam-preview" class="hidden space-y-2 text-center">
                        <div class="w-28 h-28 mx-auto rounded-2xl overflow-hidden border-2 border-emerald-500 bg-black"><img id="admcam-img" src="" class="w-full h-full object-cover"></div>
                        <div class="flex justify-center gap-2">
                            <button type="button" onclick="reopenCrop('admcam', 'adm-foto-input')" class="px-3 py-1 rounded-xl bg-amber-500/20 text-amber-300 text-xs font-bold">Acomodar</button>
                            <button type="button" onclick="resetCamUI('admcam', 'adm-foto-input')" class="px-3 py-1 rounded-xl bg-zinc-800 text-zinc-200 text-xs font-bold">Cambiar Foto</button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-blood-3d w-full py-3 rounded-xl font-extrabold text-xs uppercase text-white">Guardar Cambios</button>
            </form>
        </div>
    </div>
    <script>
        function openAdminNewModal() {
            document.getElementById("adm-modal-title").textContent = "Dar de Alta Disfraz";
            document.getElementById("adm-form").action = "<?php echo e(route('halloween.registrar')); ?>";
            document.getElementById("adm-nombre").value = "";
            document.getElementById("adm-disfraz").value = "";
            document.getElementById("adm-cat").value = "mejor";
            resetCamUI("admcam", "adm-foto-input");
            const m = document.getElementById("admin-modal"); m.classList.remove("hidden"); m.classList.add("flex");
        }
        function openAdminEditModal(id, nombre, disfraz, cat, foto) {
            document.getElementById("adm-modal-title").textContent = "Editar Participante / Foto";
            document.getElementById("adm-form").action = "/panel-secreto-admin/actualizar/" + id;
            document.getElementById("adm-nombre").value = nombre || "";
            document.getElementById("adm-disfraz").value = disfraz || "";
            document.getElementById("adm-cat").value = cat || "mejor";
            resetCamUI("admcam", "adm-foto-input");
            if (foto) {
                document.getElementById("adm-foto-input").value = foto;
                document.getElementById("admcam-img").src = foto;
                document.getElementById("admcam-prompt").classList.add("hidden");
                document.getElementById("admcam-preview").classList.remove("hidden");
            }
            const m = document.getElementById("admin-modal"); m.classList.remove("hidden"); m.classList.add("flex");
        }
        function closeAdminModal() {
            stopCam("admcam", "adm-foto-input");
            const m = document.getElementById("admin-modal"); m.classList.add("hidden"); m.classList.remove("flex");
        }
    </script>
</body>
</html>
<?php /**PATH /home/youcaffe/casaplata/resources/views/halloween/admin.blade.php ENDPATH**/ ?>