<?php

declare(strict_types=1);
require_once(__DIR__ . "/php/imports.php");
entrypoint();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once(__DIR__ . "/templates/head.php"); ?>
</head>


<body>
    <?php require_once(__DIR__ . "/templates/navbar.php"); ?>
    <main class="responsive">
        <div class="row">
            <div class="max"></div>
            <h3>CornCTF</h3>
            <div class="max"></div>
        </div>
        <div class="center-align">
            <p class="large-text">The most cracking CTFs on the web</p>
        </div>
        <div class="large-space"></div>
        <div class="center-align">
            <img src="images/favicon.png" class="responsive large-width" alt="">
        </div>
        <div class="medium-space"></div>
        <section>
            <header>
                <div class="row">
                    <i>groups</i>
                    <h5>About Corn</h5>
                </div>
            </header>
            <p class="large-text"><a class="link" target="_blank" href="https://youtu.be/WhwcUHTmqkU">CORNCTF</a> is a university project born as a joke and developed to make people who enjoy playing with computer security have fun, offering the most cracking CTFs on the web.
                CORNCTF provides various computer security challenges, each of which has a flag to be captured.
                What, for us, makes CORN so great is the variety of challenges on offer - there's something for everyone, whether you're a newbie or a seasoned pro. There's a good mix of different genres too, so you're never bored.</p>
            <p class="large-text">Last but not least the best reason to choose this site...it's CORN!</p>

        </section>
        <div class="medium-space"></div>
        <section>
            <header>
                <div class="row">
                    <i>flag</i>
                    <h5>About CTFs</h5>
                </div>
            </header>
            <p class="large-text">Capture-The-Flags (better known as CTFs) are competitions where hackers have to solve
                cybersecurity problems (jeopardy style) and/or capture and defend computer systems
                (attack-defense style). These competitions consist of a series of challenges that vary in their
                degree of difficulty, and that require participants to exercise different skillsets to solve.</p>
            <p class="large-text">
                "Flags" are bits of data that prove you have completed a given task. Players can be lone wolves
                who attempt the various challenges by themselves, or they can work with others to attempt to
                score the highest number of points as a team. CTF events are usually timed, and the points are
                totaled once the time has expired. The winning player/team will be the one that secured the
                highest score.
            </p>
        </section>
        <div class="medium-space"></div>
        <section>
            <header>
                <div class="row">
                    <i>gavel</i>
                    <h5>Rules</h5>
                </div>
            </header>
            <p class="large-text">Our site rules are very simple:</p>
            <p class="large-text"> We ask that you please be respectful to other users and refrain from using offensive
                or inappropriate phrases in your bio description, in your username and name.</p>
            <p class="large-text">There are many challenges on our site, if you want to solve one, you'll need to
                put in the effort to solve the challenge. Don't just try to brute force your way through it, as
                that'll likely just lead to frustration without gaining any competence. Instead, take the time
                to figure out the correct solution and you'll be much more likely to find the flag you're looking for.
                Trust us, it'll be worth the effort in the end.</p>
            <p class="large-text">If you have any questions about the site rules, please feel free to contact us. Thank you for your cooperation!</p>
        </section>
        <div class="medium-space"></div>
        <section>
            <header>
                <div class="row">
                    <i>history_edu</i>
                    <h5>History</h5>
                </div>
            </header>
            <p class="large-text">The <a class="link" target="_blank" href="https://en.wikipedia.org/wiki/Columbian_exchange">Columbian exchange </a>, also known as the Columbian interchange, was the widespread transfer of plants, animals, precious metals, commodities, culture, human populations, technology, diseases, and ideas between the New World (the Americas) in the Western Hemisphere, and the Old World (Afro-Eurasia) in the Eastern Hemisphere, in the late 15th and following centuries.</p>
            <p class="large-text"> It is named after the Italian explorer Christopher Columbus and is related to the European colonization and global trade following his 1492 voyage. Communicable diseases of Old World origin resulted in an 80 to 95 percent reduction in the number of Indigenous peoples of the Americas from the 15th century onwards.</p>
            <p class="large-text">The cultures of both hemispheres were significantly impacted by the migration of people (both free and enslaved) from the Old World to the New. European colonists and African slaves replaced Indigenous populations across the Americas, to varying degrees.</p>
            <p class="large-text">The new contacts among the global population resulted in the interchange of a wide variety of crops and livestock, which supported increases in food production and population in the Old World. American crops such as <a target="_blank" class="link bold">maize (CORN) </a>, potatoes, tomatoes, tobacco, cassava, sweet potatoes, and chili peppers became important crops around the world.</p>
        </section>
    </main>
    <?php require_once(__DIR__ . "/templates/footer.php"); ?>
</body>

</html>