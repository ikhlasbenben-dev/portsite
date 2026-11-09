<?php
// ---------- DATA ----------
$siteName   = 'Ikhlas Benjoudare';
$email      = 'ekhlasbenjoudare97@gmail.com';
$photo      = 'image/me.jpeg';

$navLinks = [
    '#home'     => 'Accueil',
    '#projects' => 'Projets',
    '#about'    => 'À propos',
    '#contact'  => 'Contact',
];

// Each atelier can contain photo folders, a website URL, or both.
$ateliers = [
    1 => [
        'title' => 'Atelier 1',
        'desc' => 'Atelier 1 — Document Word',
        'doc_url' => 'Atelier%201.pdf',
        'doc_label' => 'Voir Document',
        'repo_url' => '',
    ],
    2 => [
        'title' => 'Atelier 2',
        'desc' => 'Atelier 2 — Projet Deal',
        'doc_url' => 'Doc1.pdf',
        'doc_label' => 'Voir Document',
        'jira_url' => 'https://ikhlasbenben0709.atlassian.net/jira/software/projects/DEAL/boards/36/backlog',
        'figma_url' => 'https://www.figma.com/design/zwM3FU6erOokjjrCST4mK2/Deal?node-id=0-1&p=f&t=cffMicxRlI8ZxmZu-0',
    ],
    3 => [
        'title' => 'Atelier 3',
        'desc' => 'Atelier 3 — Document Word',
        'doc_url' => 'atelier3.pdf',
        'doc_label' => 'Voir Document',
        'repo_url' => '',
    ],
    4 => [
        'title' => 'Atelier 4',
        'desc' => 'Application web Deal',
        'site_url' => 'https://dealappamallechhab.vercel.app/',
        'site_label' => 'Site Web Deal',
    ],
    5 => [
        'title' => 'Atelier 5',
        'desc' => 'Atelier 5',
        'doc_url' => 'at5.pdf',
        'doc_label' => 'Voir Document',
    ],
    6 => [
        'title' => 'Atelier 6',
        'desc' => 'Inscription et liste des stagiaires — PHP, MySQL Aiven et Cloudinary',
        'site_url' => 'https://portsite-indol.vercel.app/atelier6/index.html',
        'site_label' => 'Ouvrir Atelier 6',
    ],
    7 => [
        'title' => 'Atelier 7',
        'desc' => 'GitLab CI/CD et analyse de qualité SonarQube Cloud',
        'repo_url' => 'https://gitlab.com/ikhlasbenben-dev/atelier-sonarqube',
        'repo_label' => 'Voir le projet GitLab',
        'site_url' => 'https://portsite-indol.vercel.app/atelier7/index.html',
        'site_label' => 'Ouvrir Atelier 7',
    ],
];

// Small helper for safe output
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function atelierSlug(string $text): string
{
    $slug = strtolower(trim($text));
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    return trim($slug, '-') ?: 'photos';
}

/** Render a photo-gallery button and the gallery it controls. */
function renderGalleryButton(string $label, string $folder, string $id): void
{
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $files = glob($folder . '/*') ?: [];
    $images = array_values(array_filter($files, static function ($file) use ($allowedExtensions) {
        return is_file($file)
            && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowedExtensions, true);
    }));
    natsort($images);

    $imageItems = [];
    $publicPath = realpath(__DIR__ . '/public');
    foreach ($images as $image) {
        $realImage = realpath($image);
        if ($publicPath && $realImage && strpos($realImage, $publicPath . DIRECTORY_SEPARATOR) === 0) {
            $relativePath = substr($realImage, strlen($publicPath) + 1);
            $imageItems[] = '/' . str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
        }
    }
    ?>
    <button type="button" class="atelier-button" aria-expanded="false"
        aria-controls="<?= e($id) ?>" onclick="toggleGallery(this)">
        <?= e($label) ?> (<?= count($imageItems) ?>)
    </button>
    <div class="atelier-gallery" id="<?= e($id) ?>" hidden>
        <?php if ($imageItems): ?>
            <?php foreach ($imageItems as $src): ?>
                <a href="<?= e($src) ?>" target="_blank" rel="noopener">
                    <img src="<?= e($src) ?>" alt="<?= e($label) ?>" loading="lazy">
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune photo trouvée dans ce dossier.</p>
        <?php endif; ?>
    </div>
    <?php
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?= e($siteName) ?> — Portfolio</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet" />

    <style>
        :root {
            --bg-main: #fdfaf8;
            --bg-card: #ffffff;
            --accent-pink: #fce4ec;
            --soft-pink: #f8bbd0;
            --deep-brown: #5d4037;
            --text-main: #4e342e;
            --text-muted: #8d6e63;
            --border-color: #f3e5f5;
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
        }

        /* NAVIGATION */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 8vw;
            background: rgba(253, 250, 248, 0.8);
            backdrop-filter: blur(10px);
            z-index: 1000;
            border-bottom: 1px solid var(--accent-pink);
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--deep-brown);
            text-decoration: none;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-main);
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--soft-pink);
        }

        /* HOME */
        #home {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 8vw;
        }

        .hero-container {
            display: flex;
            align-items: center;
            gap: 60px;
            max-width: 1100px;
            width: 100%;
        }

        .hero-image-placeholder {
            width: 350px;
            height: 450px;
            background: var(--accent-pink);
            border-radius: 100px 20px 100px 20px;
            border: 8px solid white;
            box-shadow: 0 20px 40px rgba(93, 64, 55, 0.1);
            flex-shrink: 0;
            overflow: hidden;
        }

        .hero-image-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            color: var(--deep-brown);
            margin-bottom: 15px;
            line-height: 1.1;
        }

        .hero-content p {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 30px;
            max-width: 500px;
        }

        .btn-contact {
            padding: 12px 35px;
            background-color: var(--deep-brown);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            transition: transform 0.3s, background 0.3s;
            display: inline-block;
        }

        .btn-contact:hover {
            background-color: var(--text-muted);
            transform: translateY(-3px);
        }

        /* PROJECTS */
        #projects {
            padding: 100px 8vw;
            background: white;
        }

        .section-title {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--deep-brown);
            margin-bottom: 60px;
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .project-card {
            background: var(--bg-main);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid var(--accent-pink);
        }

        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(248, 187, 208, 0.3);
        }

        .project-card h3 {
            margin-bottom: 15px;
            color: var(--deep-brown);
        }

        .project-card p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .project-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .project-links a {
            font-size: 13px;
            text-decoration: none;
            color: var(--deep-brown);
            font-weight: 600;
            padding: 5px 15px;
            border: 1px solid var(--deep-brown);
            border-radius: 20px;
            transition: 0.3s;
        }

        .project-links a:hover {
            background: var(--deep-brown);
            color: white;
        }

        .project-links .pending-site-button {
            font: inherit;
            font-size: 13px;
            color: var(--deep-brown);
            font-weight: 600;
            padding: 5px 15px;
            border: 1px solid var(--deep-brown);
            border-radius: 20px;
            background: transparent;
        }

        .project-links .atelier-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font: inherit;
            font-size: 13px;
            color: var(--deep-brown);
            font-weight: 600;
            padding: 5px 15px;
            border: 1px solid var(--deep-brown);
            border-radius: 20px;
            background: transparent;
            cursor: pointer;
            transition: 0.3s;
        }

        .project-links .atelier-button:hover {
            background: var(--deep-brown);
            color: white;
        }

        .atelier-gallery {
            display: flex;
            flex-basis: 100%;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 12px;
        }

        .atelier-gallery[hidden] {
            display: none;
        }

        .atelier-gallery a,
        .atelier-gallery a:hover,
        .atelier-gallery a:focus-visible {
            padding: 0;
            border: 0;
            background: transparent;
            transform: none;
        }

        .atelier-gallery img {
            display: block;
            width: 130px;
            height: 95px;
            border: 1px solid var(--accent-pink);
            border-radius: 10px;
            object-fit: cover;
        }

        .atelier-gallery p {
            color: var(--text-muted);
        }

        /* ABOUT + CONTACT */
        #about,
        #contact {
            padding: 100px 8vw;
            text-align: center;
        }

        .about-text {
            max-width: 800px;
            margin: 0 auto;
            color: var(--text-muted);
        }

        /* FOOTER */
        footer {
            padding: 40px;
            text-align: center;
            background: var(--bg-main);
            border-top: 1px solid var(--accent-pink);
            color: var(--text-muted);
            font-size: 14px;
        }

        /* ANIMATION */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .hero-container {
                flex-direction: column;
                text-align: center;
            }

            .hero-image-placeholder {
                width: 250px;
                height: 320px;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .nav-links {
                gap: 10px;
            }

            .nav-links a {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVIGATION -->
    <nav>
        <a href="#home" class="logo"><?= e($siteName) ?></a>

        <ul class="nav-links">
            <?php foreach ($navLinks as $href => $label): ?>
                <li>
                    <a href="<?= e($href) ?>"><?= e($label) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- HOME -->
    <section id="home">
        <div class="hero-container">

            <div class="hero-image-placeholder">
                <img src="<?= e($photo) ?>" alt="<?= e($siteName) ?>">
            </div>

            <div class="hero-content">
                <h1><?= e($siteName) ?></h1>

                <p>
                    Développeuse web passionnée par le design épuré
                    et les expériences utilisateur élégantes.
                </p>

                <a href="#contact" class="btn-contact">Me contacter</a>
            </div>

        </div>
    </section>

    <!-- PROJECTS -->
    <section id="projects">

        <h2 class="section-title reveal">Mes Ateliers</h2>

        <div class="projects-grid">

            <?php foreach ($ateliers as $number => $atelier): ?>

                <div class="project-card reveal">

                    <h3><?= e($atelier['title']) ?></h3>

                    <p><?= e($atelier['desc']) ?></p>

                    <div class="project-links">

                        <?php if (!empty($atelier['doc_url'])): ?>
                            <a href="<?= e($atelier['doc_url']) ?>"
                               target="_blank"
                               rel="noopener">
                                <?= e($atelier['doc_label']) ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($atelier['repo_url'])): ?>
                            <a href="<?= e($atelier['repo_url']) ?>"
                               target="_blank"
                               rel="noopener">
                                <?= e($atelier['repo_label'] ?? 'Voir lien GitHub') ?>
                            </a>
                        <?php endif; ?>

                        <?php foreach (['jira_url' => 'Voir Jira', 'figma_url' => 'Voir Figma', 'site_url' => ($atelier['site_label'] ?? 'Voir site'), 'url' => 'Visiter le site ↗'] as $urlKey => $urlLabel): ?>
                            <?php if (!empty($atelier[$urlKey])): ?>
                                <a href="<?= e($atelier[$urlKey]) ?>" target="_blank" rel="noopener">
                                    <?= e($urlKey === 'url' ? ($atelier['label'] ?? $urlLabel) : $urlLabel) ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if (!empty($atelier['site_button_pending'])): ?>
                            <button type="button" class="pending-site-button" disabled>Voir site</button>
                        <?php endif; ?>

                        <?php foreach (($atelier['folders'] ?? []) as $folderName): ?>
                            <?php
                                $label = $folderName !== '' ? $folderName : 'Photos';
                                $folder = __DIR__ . '/public/images/ateliers/Atelier ' . (int) $number
                                    . ($folderName !== '' ? '/' . $folderName : '');
                                $galleryId = 'gallery-' . (int) $number . '-' . atelierSlug($label);
                                renderGalleryButton($label, $folder, $galleryId);
                            ?>
                        <?php endforeach; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>
    </section>

    <!-- ABOUT -->
    <section id="about" class="reveal">

        <h2 class="section-title">À propos</h2>

        <div class="about-text">
            <p>
                Étudiante passionnée par le monde digital,
                je m'efforce de créer des solutions web qui allient
                performance technique et esthétique raffinée.
                Spécialisée en HTML, CSS, JavaScript et PHP.
            </p>
        </div>

    </section>

    <!-- CONTACT -->
    <section id="contact" class="reveal">

        <h2 class="section-title">Contact</h2>

        <div class="about-text">
            <p>
                Vous pouvez me contacter pour discuter
                d'un projet ou d'une collaboration.<br><br>

                Mon email :
                <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
            </p>
        </div>

    </section>

    <!-- FOOTER -->
    <footer>
        <p>&copy; <?= date('Y') ?> <?= e($siteName) ?> — Créé avec passion</p>
    </footer>

    <!-- JAVASCRIPT -->
    <script>
        function toggleGallery(button) {
            var galleryId = button.getAttribute("aria-controls");
            var gallery = document.getElementById(galleryId);
            var isOpen = button.getAttribute("aria-expanded") === "true";

            button.setAttribute("aria-expanded", String(!isOpen));
            gallery.hidden = isOpen;
        }

        function reveal() {
            var reveals = document.querySelectorAll(".reveal");

            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;

                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }

        window.addEventListener("scroll", reveal);
        reveal();
    </script>

</body>
</html>
