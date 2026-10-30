<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Votación en Vivo - Casa Plata</title>
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
    <div class="relative z-10 max-w-xl mx-auto px-4 py-5">
        <div class="flex items-center justify-between gap-2 pb-4 border-b border-red-900/40">
            <a href="<?php echo e(route('halloween.index')); ?>" class="px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-800 text-xs font-bold text-zinc-300 flex items-center gap-1.5 active:scale-95">
                <i class="fa-solid fa-chevron-left text-red-500"></i> Inicio
            </a>
            <div class="text-center">
                <h2 class="font-cinzel font-black text-lg sm:text-2xl text-white tracking-wider leading-none glow-silver">CASA PLATA</h2>
                <span class="text-[10px] font-bold uppercase tracking-widest text-red-500">Concurso en Vivo</span>
            </div>
            <?php if($config && $config->registro_activo && !$config->resultados_finalizados): ?>
                <button type="button" onclick="openRegModal()" class="px-3 py-2 rounded-xl bg-red-950/60 border border-red-800/60 text-xs font-bold text-orange-300 flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-camera"></i> <span>Inscribirme</span>
                </button>
            <?php else: ?>
                <span class="w-16"></span>
            <?php endif; ?>
        </div>

        <?php if(session('status')): ?>
            <div class="my-4 p-3.5 rounded-xl bg-emerald-950/90 border border-emerald-600 text-xs font-bold text-emerald-300 flex items-center gap-2 shadow-lg">
                <i class="fa-solid fa-circle-check text-base"></i> <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="my-4 p-3.5 rounded-xl bg-red-950/90 border border-red-600 text-xs font-bold text-red-300 flex items-center gap-2 shadow-lg">
                <i class="fa-solid fa-triangle-exclamation text-base"></i> <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($config && $config->resultados_finalizados): ?>
            <div class="my-6 space-y-5">
                <div class="bg-gradient-to-b from-amber-950/40 via-zinc-950 to-zinc-950 border-2 border-amber-500/70 rounded-3xl p-5 sm:p-6 text-center shadow-[0_0_50px_rgba(245,158,11,0.2)]">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-400/50 mx-auto flex items-center justify-center text-3xl mb-2 animate-float">👑</div>
                    <span class="text-[10px] font-extrabold uppercase tracking-[0.25em] text-amber-400 block">Votaciones Cerradas • Conteo Oficial</span>
                    <h3 class="font-cinzel font-black text-2xl sm:text-3xl text-white uppercase tracking-wider mt-1 glow-silver">Ganadores Casa Plata</h3>
                    <p class="text-xs text-zinc-400 mt-1">Tabla oficial del Top 1 al 10 de la noche</p>
                    <div class="flex flex-wrap items-center justify-center gap-2 mt-4">
                        <a href="<?php echo e(route('halloween.votar', ['top_cat' => 'todas'])); ?>" class="px-3.5 py-2 rounded-xl text-xs font-extrabold <?php echo e(($topCat ?? 'todas') === 'todas' ? 'bg-amber-500 text-black shadow' : 'bg-zinc-900 text-zinc-300 border border-zinc-700'); ?>">🏅 Top 10 General</a>
                        <a href="<?php echo e(route('halloween.votar', ['top_cat' => 'mejor'])); ?>" class="px-3.5 py-2 rounded-xl text-xs font-extrabold <?php echo e(($topCat ?? 'todas') === 'mejor' ? 'bg-amber-500 text-black shadow' : 'bg-zinc-900 text-zinc-300 border border-zinc-700'); ?>">🏆 Mejor Disfraz</a>
                        <a href="<?php echo e(route('halloween.votar', ['top_cat' => 'puti'])); ?>" class="px-3.5 py-2 rounded-xl text-xs font-extrabold <?php echo e(($topCat ?? 'todas') === 'puti' ? 'bg-amber-500 text-black shadow' : 'bg-zinc-900 text-zinc-300 border border-zinc-700'); ?>">🔥 Puti Disfraz</a>
                    </div>
                </div>
                <div class="space-y-3 pb-8">
                    <?php $__empty_1 = true; $__currentLoopData = $top10Publico; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $rank = $idx + 1;
                            $cardBorder = "border-zinc-800 bg-zinc-950/90";
                            if ($rank === 1) $cardBorder = "border-amber-500/80 bg-gradient-to-r from-amber-950/35 via-zinc-950 to-zinc-950 shadow-[0_0_25px_rgba(245,158,11,0.15)]";
                            elseif ($rank === 2) $cardBorder = "border-zinc-400/60 bg-zinc-950";
                            elseif ($rank === 3) $cardBorder = "border-amber-700/60 bg-zinc-950";
                            $fotoTop = $t->foto ?: "https://placehold.co/600x600/18181b/f97316?text=Foto";
                        ?>
                        <div class="border <?php echo e($cardBorder); ?> rounded-2xl p-3.5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <?php if($rank === 1): ?> <span class="w-10 h-10 rounded-xl bg-amber-500 text-black font-black text-sm flex items-center justify-center shrink-0 shadow-lg">🥇 1</span>
                                <?php elseif($rank === 2): ?> <span class="w-10 h-10 rounded-xl bg-zinc-300 text-black font-black text-sm flex items-center justify-center shrink-0">🥈 2</span>
                                <?php elseif($rank === 3): ?> <span class="w-10 h-10 rounded-xl bg-amber-700 text-white font-black text-sm flex items-center justify-center shrink-0">🥉 3</span>
                                <?php else: ?> <span class="w-9 h-9 rounded-xl bg-zinc-900 border border-zinc-700 text-zinc-300 font-black text-xs flex items-center justify-center shrink-0">#<?php echo e($rank); ?></span> <?php endif; ?>
                                <div onclick="openLightbox(<?php echo e(json_encode($fotoTop)); ?>, <?php echo e(json_encode($t->nombre_disfraz)); ?>, <?php echo e(json_encode($t->nombre_invitado)); ?>)" class="relative shrink-0 cursor-pointer group">
                                    <img src="<?php echo e($fotoTop); ?>" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border border-zinc-700 bg-zinc-900 group-hover:scale-105 transition">
                                    <span class="absolute bottom-1 right-1 w-6 h-6 rounded-lg bg-black/80 text-white text-[10px] flex items-center justify-center border border-white/20"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider <?php echo e($t->categoria === 'mejor' ? 'text-amber-400' : 'text-red-400'); ?> block"><?php echo e($t->categoria === 'mejor' ? '🏆 Mejor Disfraz' : '🔥 Puti Disfraz'); ?></span>
                                    <h4 class="font-extrabold text-sm sm:text-base text-white truncate"><?php echo e($t->nombre_disfraz); ?></h4>
                                    <p class="text-xs text-zinc-400 truncate">Por: <?php echo e($t->nombre_invitado); ?></p>
                                </div>
                            </div>
                            <div class="text-right shrink-0 pl-2">
                                <span class="text-xl sm:text-2xl font-black text-amber-400 block leading-none"><?php echo e($t->votos); ?></span>
                                <span class="text-[10px] text-zinc-500 uppercase font-bold">Votos</span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-8 text-center text-zinc-500 bg-zinc-950 rounded-2xl border border-zinc-800">Sin participantes registrados.</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif(!$config || !$config->votaciones_activas): ?>
            <div class="my-8 bg-zinc-950/95 border-2 border-red-900/80 rounded-3xl p-7 text-center shadow-2xl space-y-4 metal-plate relative">
                <div class="rivet absolute top-3 left-3"></div><div class="rivet absolute top-3 right-3"></div>
                <div class="rivet absolute bottom-3 left-3"></div><div class="rivet absolute bottom-3 right-3"></div>
                <div class="w-16 h-16 rounded-2xl bg-red-950/60 border border-red-700/50 mx-auto flex items-center justify-center text-3xl text-red-500">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <h3 class="font-cinzel font-black text-2xl text-white uppercase">Votaciones en Espera</h3>
                <p class="text-xs sm:text-sm text-zinc-400 max-w-sm mx-auto leading-relaxed">El organizador de <strong class="text-white">Casa Plata</strong> aún no ha abierto las votaciones. ¡Prepara tu disfraz!</p>
                <?php if($config && $config->registro_activo): ?>
                    <div class="pt-2">
                        <button type="button" onclick="openRegModal()" class="btn-blood-3d px-6 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider text-white">
                            <i class="fa-solid fa-camera mr-1"></i> Inscribir mi Disfraz Ahora
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 gap-2.5 my-4">
                <a href="<?php echo e(route('halloween.votar', ['categoria' => 'mejor'])); ?>" class="p-3 rounded-2xl border-2 <?php echo e($categoria === 'mejor' ? 'border-amber-500 bg-amber-500/15' : 'border-zinc-800 bg-zinc-900/60'); ?> flex items-center justify-between">
                    <div><span class="text-[10px] font-bold uppercase text-amber-300/80 block">Categoría 1</span><span class="font-extrabold text-xs sm:text-sm text-amber-400">🏆 MEJOR DISFRAZ</span></div>
                    <span class="text-xs font-black bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded-lg"><?php echo e($countMejor); ?></span>
                </a>
                <a href="<?php echo e(route('halloween.votar', ['categoria' => 'puti'])); ?>" class="p-3 rounded-2xl border-2 <?php echo e($categoria === 'puti' ? 'border-red-500 bg-red-500/15' : 'border-zinc-800 bg-zinc-900/60'); ?> flex items-center justify-between">
                    <div><span class="text-[10px] font-bold uppercase text-zinc-400 block">Categoría 2</span><span class="font-extrabold text-xs sm:text-sm text-red-500">🔥 PUTI DISFRAZ</span></div>
                    <span class="text-xs font-black bg-red-500/20 text-red-300 px-2 py-0.5 rounded-lg"><?php echo e($countPuti); ?></span>
                </a>
            </div>
            <div class="space-y-3 pb-10">
                <?php $__empty_1 = true; $__currentLoopData = $concursantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $fotoItem = $item->foto ?: "https://placehold.co/600x600/18181b/f97316?text=Disfraz"; ?>
                    <div class="bg-zinc-950/90 border border-zinc-800 rounded-2xl p-3.5 flex items-center justify-between gap-3.5 shadow-lg">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div onclick="openLightbox(<?php echo e(json_encode($fotoItem)); ?>, <?php echo e(json_encode($item->nombre_disfraz)); ?>, <?php echo e(json_encode($item->nombre_invitado)); ?>)" class="relative shrink-0 cursor-pointer group">
                                <img src="<?php echo e($fotoItem); ?>" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-2 border-red-900/70 bg-zinc-900 group-hover:border-orange-500 transition shadow-md">
                                <span class="absolute bottom-1.5 right-1.5 px-1.5 py-0.5 rounded-md bg-black/80 text-zinc-200 text-[10px] font-bold flex items-center gap-1 border border-white/15">
                                    <i class="fa-solid fa-magnifying-glass-plus text-orange-400"></i> Ver
                                </span>
                            </div>
                            <div class="min-w-0 space-y-1">
                                <h3 class="font-extrabold text-sm sm:text-base text-white leading-snug"><?php echo e($item->nombre_disfraz); ?></h3>
                                <p class="text-xs text-zinc-400 truncate">Por: <span class="text-zinc-200 font-semibold"><?php echo e($item->nombre_invitado); ?></span></p>
                            </div>
                        </div>
                        <form method="POST" action="<?php echo e(route('halloween.emitirVoto', $item->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-blood-3d px-4 py-3 rounded-xl font-extrabold text-xs uppercase text-white">Votar</button>
                        </form>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-8 text-center text-zinc-400 bg-zinc-950/90 rounded-2xl border border-zinc-800 space-y-3">
                        <p>Aún no hay disfraces inscritos en esta categoría.</p>
                        <?php if($config && $config->registro_activo): ?>
                            <button type="button" onclick="openRegModal()" class="btn-blood-3d px-4 py-2 rounded-xl text-xs font-extrabold text-white uppercase">+ Inscribir el Primero</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- MODAL DE INSCRIPCIÓN CON CÁMARA, GALERÍA Y ENCUADRE TÁCTIL -->
    <div id="modal-reg" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/85 backdrop-blur-sm overflow-y-auto">
        <div class="bg-zinc-950 border border-red-900/70 rounded-2xl max-w-md w-full p-5 sm:p-6 relative my-auto shadow-2xl">
            <button type="button" onclick="closeRegModal()" class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-zinc-900 text-zinc-400 hover:text-white flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h3 class="font-cinzel font-black text-xl text-white uppercase">Inscribir Disfraz</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-4">Tómate una foto o elígela de tu galería y acomódala con tu dedo.</p>
            <form method="POST" action="<?php echo e(route('halloween.registrar')); ?>" onsubmit="ensureCroppedBeforeSubmit('regcam', 'reg-foto-input')" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="foto" id="reg-foto-input" value="">
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-300 mb-1">Tu Nombre o Apodo</label>
                    <input type="text" name="nombre_invitado" required placeholder="Ej. Alejandro / La Catrina" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-300 mb-1">Nombre del Disfraz</label>
                    <input type="text" name="nombre_disfraz" required placeholder="Ej. Demonio de Ceniza" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-300 mb-1">Categoría</label>
                    <select name="categoria" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-sm text-white">
                        <option value="mejor" <?php echo e(($categoria ?? 'mejor') === 'mejor' ? 'selected' : ''); ?>>🏆 MEJOR DISFRAZ</option>
                        <option value="puti" <?php echo e(($categoria ?? 'mejor') === 'puti' ? 'selected' : ''); ?>>🔥 MEJOR PUTI DISFRAZ 🔥</option>
                    </select>
                </div>
                <div class="bg-zinc-900/90 border border-zinc-800 rounded-2xl p-3.5 space-y-3">
                    <label class="text-xs font-bold uppercase text-orange-400 flex items-center gap-1.5"><i class="fa-solid fa-camera"></i> Fotografía del Disfraz</label>
                    <div id="regcam-prompt" class="bg-zinc-950 border border-amber-500/40 rounded-xl p-3.5 text-center space-y-2.5">
                        <div class="w-10 h-10 rounded-full bg-amber-500/15 border border-amber-500/40 mx-auto flex items-center justify-center text-amber-400 text-lg"><i class="fa-solid fa-shield-halved"></i></div>
                        <p class="text-xs font-bold text-white">Aviso antes de encender tu cámara</p>
                        <p class="text-[11px] text-zinc-400 leading-relaxed">Para que los invitados reconozcan tu disfraz al votar, necesitamos tomarte una foto. Al tocar el botón, tu teléfono te preguntará si deseas <strong class="text-amber-300">Permitir el uso de la cámara</strong>, o puedes elegir una foto de tu galería.</p>
                        <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                            <button type="button" onclick="initCam('regcam')" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs flex items-center gap-1.5 shadow"><i class="fa-solid fa-video"></i> Entendido, Encender Cámara</button>
                            <label class="px-3 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-bold text-xs cursor-pointer flex items-center gap-1.5 border border-zinc-700">
                                <i class="fa-solid fa-images text-orange-400"></i> Elegir de Galería
                                <input type="file" id="regcam-file" accept="image/*" onchange="handleFileImg(event, 'regcam')" class="hidden">
                            </label>
                        </div>
                    </div>
                    <div id="regcam-live" class="hidden space-y-2.5 text-center">
                        <div class="relative rounded-xl overflow-hidden bg-black aspect-square max-h-56 mx-auto border border-red-800/60">
                            <video id="regcam-video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        </div>
                        <div class="flex justify-center gap-2">
                            <button type="button" onclick="snapPhoto('regcam')" class="btn-blood-3d px-4 py-2 rounded-xl font-extrabold text-xs text-white uppercase"><i class="fa-solid fa-camera-retro mr-1"></i> Capturar Foto</button>
                            <button type="button" onclick="stopCam('regcam', 'reg-foto-input')" class="px-3 py-2 rounded-xl bg-zinc-800 text-zinc-300 text-xs font-bold">Cancelar</button>
                        </div>
                    </div>
                    <div id="regcam-cropper" class="hidden space-y-2.5 text-center bg-zinc-950 p-3 rounded-xl border border-amber-500/50">
                        <p class="text-[11px] font-bold text-amber-300"><i class="fa-solid fa-arrows-up-down-left-right mr-1"></i> Arrastra la foto con tu dedo para encuadrarla</p>
                        <div class="relative w-52 h-52 mx-auto rounded-2xl overflow-hidden border-2 border-amber-500 bg-black touch-none select-none cursor-move">
                            <canvas id="regcam-crop-canvas" width="700" height="700" class="w-full h-full block"></canvas>
                        </div>
                        <div class="flex items-center gap-2 px-3 pt-1">
                            <i class="fa-solid fa-magnifying-glass-minus text-xs text-zinc-400"></i>
                            <input type="range" id="regcam-crop-zoom" min="1" max="3" step="0.02" value="1" oninput="zoomCrop('regcam', this.value)" class="w-full accent-amber-500 cursor-pointer h-1.5 bg-zinc-800 rounded-lg">
                            <i class="fa-solid fa-magnifying-glass-plus text-xs text-amber-400"></i>
                        </div>
                        <div class="flex justify-center gap-2 pt-1">
                            <button type="button" onclick="confirmCrop('regcam', 'reg-foto-input')" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-extrabold text-xs uppercase shadow"><i class="fa-solid fa-check mr-1"></i> Confirmar Encuadre</button>
                            <button type="button" onclick="resetCamUI('regcam', 'reg-foto-input')" class="px-3 py-2 rounded-xl bg-zinc-800 text-zinc-300 text-xs font-bold">Cambiar Foto</button>
                        </div>
                    </div>
                    <div id="regcam-preview" class="hidden space-y-2.5 text-center">
                        <div class="w-32 h-32 mx-auto rounded-2xl overflow-hidden border-2 border-emerald-500 bg-black">
                            <img id="regcam-img" src="" class="w-full h-full object-cover">
                        </div>
                        <div class="flex justify-center gap-2">
                            <button type="button" onclick="reopenCrop('regcam', 'reg-foto-input')" class="px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/40"><i class="fa-solid fa-crop-simple mr-1"></i> Acomodar</button>
                            <button type="button" onclick="resetCamUI('regcam', 'reg-foto-input')" class="px-3 py-1.5 rounded-xl bg-zinc-800 text-zinc-200 text-xs font-bold border border-zinc-700"><i class="fa-solid fa-rotate mr-1"></i> Otra Foto</button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-blood-3d w-full py-3.5 rounded-xl font-extrabold text-xs uppercase text-white">Guardar y Publicar Disfraz</button>
            </form>
        </div>
    </div>
    <script>
        function openRegModal() {
            if (typeof resetCamUI === "function") resetCamUI("regcam", "reg-foto-input");
            const m = document.getElementById("modal-reg");
            if (m) { m.classList.remove("hidden"); m.classList.add("flex"); }
        }
        function closeRegModal() {
            if (typeof stopCam === "function") stopCam("regcam", "reg-foto-input");
            const m = document.getElementById("modal-reg");
            if (m) { m.classList.add("hidden"); m.classList.remove("flex"); }
        }
        window.addEventListener("load", function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get("inscribir") === "1") openRegModal();
        });
    </script>
</body>
</html>
<?php /**PATH /home/youcaffe/casaplata/resources/views/halloween/votar.blade.php ENDPATH**/ ?>