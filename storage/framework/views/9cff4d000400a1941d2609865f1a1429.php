<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Organizador - Casa Plata</title>
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
<body class="min-h-[100dvh] flex items-center justify-center p-4 relative">
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
    <div class="relative z-10 max-w-sm w-full bg-zinc-950/95 border border-red-900/60 rounded-3xl p-6 shadow-2xl metal-plate">
        <div class="rivet absolute top-3 left-3"></div><div class="rivet absolute top-3 right-3"></div>
        <div class="rivet absolute bottom-3 left-3"></div><div class="rivet absolute bottom-3 right-3"></div>
        <div class="text-center space-y-2 mb-6">
            <div class="w-12 h-12 rounded-2xl bg-red-950 border border-red-700/50 mx-auto flex items-center justify-center text-red-400 text-xl">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h2 class="font-cinzel font-black text-xl text-white uppercase tracking-wider pt-1">Acceso Organizador</h2>
            <p class="text-xs text-zinc-400">Panel exclusivo de Casa Plata</p>
        </div>
        <?php if(session('error')): ?>
            <div class="mb-4 p-3 rounded-xl bg-red-950 border border-red-700 text-xs font-bold text-red-300 text-center"><?php echo e(session('error')); ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('halloween.admin.login')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-300 mb-1">Usuario Admin</label>
                <input type="text" name="username" required class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-sm text-white">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-300 mb-1">Contraseña</label>
                <input type="password" name="password" required class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-sm text-white">
            </div>
            <button type="submit" class="btn-blood-3d w-full py-3 rounded-xl font-extrabold text-xs uppercase text-white">
                <i class="fa-solid fa-unlock-keyhole mr-1"></i> Entrar al Panel
            </button>
        </form>
    </div>
</body>
</html>
<?php /**PATH /home/youcaffe/casaplata/resources/views/halloween/admin_login.blade.php ENDPATH**/ ?>