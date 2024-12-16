<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Classification and Herbarium Specimens</title>
    <!-- STYLE CSS LINK -->
    <link rel="stylesheet" href="style/style.css">
    <!-- STYLE CSS LINK -->

    <!-- BOOTSTRAP CDN LINK -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- BOOTSTRAP CDN LINK -->

    <!-- FONT AWESOME CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- FONT AWESOME CDN -->

    <!-- GOOGLE FONTS LINK -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@600&display=swap" rel="stylesheet">
    <!-- GOOGLE FONTS LINK -->
</head>

<body>

    <?php include 'navbar.php'; ?>
    <div class="tutorial-wrapper">
        <section class="hero">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="copy" data-aos="fade-up" data-aos-duration="3000">
                            <div class="text-label">
                                Enhance Your Knowledge and Skills
                            </div>
                            <div class="text-hero-bold">
                                Learn with Our Comprehensive Tutorials
                            </div>
                            <div class="text-hero-regular">
                                Explore detailed and well-structured tutorials that guide you through various topics,
                                helping you expand your expertise and succeed in your projects.
                            </div>
                            <div class="cta">
                                <a href="#" class="classify-button" onclick="scrollToSection('next-section')">Start
                                    Learning</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 tuto-img">
                        <figure class="about-image">
                            <img src="img/herbarium1.png" alt="" height="500">
                            <img src="img/herbarium2.png" alt="" class="about-img">
                        </figure>
                    </div>
                </div>
            </div>
        </section>


        <!-- services section starts -->

        <section class="services">

            <h1 class="heading" id="next-section">Tutorial <span>step by step</span></h1>

            <div class="box-container">
                <div class="box">
                    <data class="card-number" value="01">01</data>
                    <img src="img/tuto1.png" alt="img">
                    <h3>Collecting Specimens</h3>
                    <p>Gather plants from diverse areas like pathways, edges of fields, and woodland borders. Avoid
                        private property and nature preserves. Collect healthy, flowering specimens along with all parts
                        for identification, noting unique characteristics and the collection date for future reference.
                    </p>
                </div>

                <div class="box">
                    <data class="card-number" value="02">02</data>
                    <img src="img/tuto2.png" alt="img">
                    <h3>Preparing Specimens</h3>
                    <p>Protect specimens during transport using sealable bags and sturdy containers. Press plants
                        immediately upon returning. For slightly wilted plants, rehydrate before pressing. Use thin
                        plywood and parchment paper to press the plants, ensuring even moisture removal and careful
                        arrangement to highlight key features.</p>
                </div>

                <div class="box">
                    <data class="card-number" value="03">03</data>
                    <img src="img/tuto3.png" alt="img">
                    <h3>Pressing Specimens</h3>
                    <p>Place prepared plants in a plant press, separated by parchment. Apply consistent pressure to keep
                        them flat, checking for moisture and rearranging if necessary. Leave specimens in the press for
                        about a week until fully dry, adjusting pressure as needed.</p>
                </div>

                <div class="box">
                    <data class="card-number" value="04">04</data>
                    <img src="img/tuto4.png" alt="img">
                    <h3>Mounting Specimens</h3>
                    <p>Once dry, mount specimens on acid-free paper using a flexible adhesive. Minimize handling to
                        avoid damage, using tools like tweezers to position plants carefully. Ensure all parts are
                        securely adhered, and touch up any loose elements with glue as needed.</p>
                </div>

                <div class="box">
                    <data class="card-number" value="05">05</data>
                    <img src="img/tuto5.png" alt="img">
                    <h3>Freezing and Storing Specimens</h3>
                    <p>Place mounted specimens between parchment layers and wrap them tightly. Freeze for 72 hours to
                        eliminate pests. Store specimens in acid-free sleeves, labeling them with relevant information.
                        Keep them in wooden cases with cedar shavings for protection against light and insects, checking
                        regularly for any damage.</p>
                </div>

            </div>

        </section>

        <!-- services section ends -->



        <!-- banner section starts -->
        <h1 class="tutorial-heading">Tools <span>needed</span></h1>
        <section class="banner-container">
            <div class="banner">
                <img src="img/tool1.png" alt="">
                <div class="content">
                    <span>Scissors</span>
                </div>

            </div>

            <div class="banner">

                <img src="img/tool2.png" alt="">
                <div class="content">
                    <span>Sealable Plastic Bags</span>
                </div>

            </div>

            <div class="banner">
                <img src="img/tool3.png" alt="">
                <div class="content">
                    <span>Thin Plywood</span>
                </div>
            </div>

            <div class="banner">

                <img src="img/tool4.png" alt="">
                <div class="content">
                    <span>Parchment Paper</span>
                </div>

            </div>

            <div class="banner">

                <img src="img/tool5.png" alt="">
                <div class="content">
                    <span>Acid-Free Mounting Paper</span>
                </div>

            </div>

            <div class="banner">

                <img src="img/tool6.png" alt="">
                <div class="content">
                    <span>Tweezers</span>
                </div>

            </div>

        </section>

        <!-- banner section ends -->



        <!-- feature section starts -->

        <section class="feature" id="feature">
            <h1 class="heading"><span>How to</span> Preserve</h1>
            <div class="box-container">
                <div class="box">
                    <img src="img/tip1.png" alt="img">
                    <h2>Choosing the Right Materials</h2>
                    <p>Select high-quality materials such as mounting paper, parchment paper and a plant press for
                        effective
                        preservation.</p>
                </div>

                <div class="box">
                    <img src="img/tip2.png" alt="img">
                    <h2>Handling Specimens</h2>
                    <p>Use gloves and handle specimens carefully to prevent damage and preserve their condition.</p>
                </div>

                <div class="box">
                    <img src="img/tip3.png" alt="img">
                    <h2>Pest Control</h2>
                    <p>Utilize pest control methods such as freezing specimens for a few days to kill pests
                        to protect your collection.</p>
                </div>

                <div class="box">
                    <img src="img/tip4.png" alt="img">
                    <h2>Storage Tips</h2>
                    <p>Store your preserved specimens properly to ensure their longevity and protect them from damage.
                    </p>
                </div>


            </div>
        </section>


        <!-- feature section ends -->





    </div>


    <?php include 'footer.php'; ?>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function scrollToSection(sectionId) {
        const section = document.getElementById(sectionId);
        section.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
    </script>
</body>

</html>