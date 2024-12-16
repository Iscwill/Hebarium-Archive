<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Classification</title>
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
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="copy" data-aos="fade-up" data-aos-duration="3000">
                            <div class="text-label">
                                Discover the World of Plant Classification
                            </div>
                            <div class="text-hero-bold">
                                Unleash Your Curiosity about Plants
                            </div>
                            <div class="text-hero-regular">
                                Dive into the fascinating realm of plant taxonomy, where you'll learn about the
                                relationships between families, genera, and species.
                            </div>
                            <div class="cta">
                                <a href="#" class="classify-button" onclick="scrollToSection('next-section')">Start
                                    Exploring</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 tuto-img">
                        <figure class="classify-image">
                            <img src="img/herbarium1.png" alt="" height="500">
                            <img src="img/herbarium2.png" alt="" class="classify-img">
                        </figure>
                    </div>
                </div>
            </div>
        </section>


    </div>



    <!-- Plant Classification Content Start -->
    <section class="classification-section container py-5">
        <div class="text-center mt-5" id="next-section">
            <h2>Plant <span>Classification</span></h2>
            <p>Understand the structure of plant taxonomy: Families, Genus, and Species.</p>
        </div>

        <!-- Explanation Section -->
        <div class="classify-explanation row mb-5 ">
            <div class="col-md-6">
                <h3>Plant Classification: Families, Genus, and Species</h3>
                <p><span class="terms">Family:</span> A family is a broader group of related plants. It includes
                    multiple genera
                    that share similar traits.</p>
                <p><span class="terms">Genus:</span> A genus is a group of species that are closely related and
                    share a
                    common
                    ancestor. Species within a genus often look similar.</p>
                <p><span class="terms">Species:</span> Species are the basic unit of classification. A species is a
                    group of
                    individuals that can reproduce and share most traits.</p>
            </div>
            <div class="col-md-6">
                <img src="img/main_top.png" alt="Plant Classification Diagram" class="top-img-fixed">
            </div>
        </div>




        <section class="example" id="example">

            <h1 class="heading">Example Plant: <span>Rosaceae Family</span></h1>

            <div class="box-container">

                <div class="box">
                    <div class="image">
                        <img src="img/rosaceae.png" alt="">
                        <div class="share">
                            <p>Family: Rosaceae</p>
                        </div>
                    </div>
                    <div class="content">

                        <span>The Rosaceae family includes many flowering plants such as roses, apples, and
                            strawberries.</span>
                    </div>
                </div>

                <div class="box">
                    <div class="image">
                        <img src="img/rosa.png" alt="">
                        <div class="share">
                            <p>Genus: Rosa</p>
                        </div>
                    </div>
                    <div class="content">

                        <span>The genus Rosa includes many species of roses, known for their flowers and
                            fragrance.</span>
                    </div>
                </div>

                <div class="box">
                    <div class="image">
                        <img src="img/rosagallica.png" alt="">
                        <div class="share">
                            <p>Species: Rosa gallica</p>
                        </div>
                    </div>
                    <div class="content">

                        <span>Rosa gallica, also known as the French rose, is a species in the genus Rosa that is
                            historically significant in perfume production.</span>
                    </div>
                </div>


            </div>

        </section>



        <!-- Herbarium Specimens Section -->
        <div class="herbarium_example row mt-5 ">
            <div class="col-md-12">
                <h3 class="heading">Herbarium <span>Specimens</span></h3>
                <p class="text-center">Below are the example of the specimens from the Rosaceae Family.</p>
            </div>

            <div class="ctr-accordion d-flex justify-content-center">
                <div class="tab">
                    <p></p>
                    <img src="img/herbarium1.png">
                </div>
                <div class="tab">
                    <img src="img/herbarium2.png">
                </div>
                <div class="tab ">
                    <img src="img/herbarium3.jpg">
                </div>
            </div>
        </div>

    </section>
    <!-- Plant Classification Content End -->


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