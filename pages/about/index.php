<?php
/**
 * About Page - Về MeowTea Fresh
 * Trang giới thiệu về công ty
 */

require_once '../../functions.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Về MeowTea Fresh - MeowTea Fresh</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* About Page Specific Styles */
        .about-page {
            padding-top: 40px;
            padding-bottom: 60px;
        }

        .about-title {
            font-size: 48px;
            font-weight: bold;
            color: var(--primary-green);
            text-align: center;
            margin-bottom: 30px;
        }

        .about-intro {
            max-width: 900px;
            margin: 0 auto 50px;
            text-align: center;
            color: var(--text-light);
            line-height: 1.8;
            font-size: 16px;
        }

        .about-hero-image {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto 50px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .about-hero-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        .about-content {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
            color: var(--text-light);
            line-height: 1.8;
            font-size: 16px;
        }

        .about-back-to-top {
            text-align: center;
            margin-top: 50px;
            padding: 20px 0;
        }

        .about-back-to-top-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-green);
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .about-back-to-top-link:hover {
            color: var(--light-green);
            transform: translateY(-3px);
        }

        .about-back-to-top-link svg {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 768px) {
            .about-title {
                font-size: 36px;
            }

            .about-intro,
            .about-content {
                font-size: 14px;
                padding: 0 20px;
            }
        }
    </style>
</head>
<body>
    <?php include '../../components/header.php'; ?>

    <!-- About Page Content -->
    <section class="about-page section">
        <div class="container">
            <h1 class="about-title">Về MeowTea Fresh</h1>
            
            <div class="about-intro">
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                    Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
                    Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                </p>
            </div>

            <div class="about-hero-image">
                <img src="../../assets/img/about/about.jpg" alt="MeowTea Fresh - Coffee Plantation">
            </div>

            <div class="about-content">
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                    Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
                    Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
                    Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </p>
            </div>

            <div class="about-back-to-top">
                <a href="#top" class="about-back-to-top-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 15l-6-6-6 6"/>
                    </svg>
                    <span>Lên đầu trang</span>
                </a>
            </div>
        </div>
    </section>

    <?php include '../../components/footer.php'; ?>

    <script src="../../assets/js/main.js"></script>
    <script>
        // Smooth scroll to top
        $(document).ready(function() {
            $('a[href="#top"]').on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: 0
                }, 600);
            });
        });
    </script>
</body>
</html>
