<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    

<nav class="navbar">

    <div class="logo">
        Event<span>Hub</span>
    </div>

    <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="#events">Events</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>

<a href="login.php" class="login">Login</a>

<a href="register.php" class="register">Register</a>

</nav>
<!-- ================= HERO ================= -->
<section class="hero" id="home">

    <div class="hero-content">

        <p class="small-title">WELCOME TO EVENTHUB</p>

        <h1>
            Create Memories.<br>
            <span>Manage Events.</span>
        </h1>

        <p>
            Discover exciting events, register easily,
            and enjoy unforgettable experiences.
        </p>

        <div class="hero-buttons">
            <a href="#events" class="btn">Explore Events</a>
            <a href="#about" class="btn-outline">Learn More</a>
        </div>

    </div>

    <div class="hero-image">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRf61tCs4SZjwmz9TlH_EwLPnhUE4LgENhZnI2_zbm92w&s=10" alt="Event Management">
    </div>

</section>
<!-- ================= EVENTS ================= -->

<section class="events" id="events">

    <div class="section-title">
        <p>DISCOVER</p>
        <h2>Upcoming Events</h2>
        <span>Find the perfect event for you</span>
    </div>


    <div class="event-container">

        <!-- Event 1 -->

        <div class="event-card">

            <img src="https://bcp.cdnchinhphu.vn/zoom/670_420/344443456812359680/2025/11/21/techfest-176371018792046500202-32-0-518-777-crop-17637101902001608112540.jpg" alt="Tech Fest">

            <div class="event-info">

                <span class="category">Technology</span>

                <h3>Tech Fest 2026</h3>

                <p>📅 25 August 2026</p>

                <p>📍 Auditorium Hall</p>

                <p>
                    Explore technology, coding and
                    innovative projects.
                </p>

                <a href="#" class="card-btn">View Details</a>

            </div>

        </div>


        <!-- Event 2 -->

        <div class="event-card">

            <img src="https://5.imimg.com/data5/RE/OU/GLADMIN-60885875/indian-cultural-eventz-500x500.png" alt="Cultural Festival">

            <div class="event-info">

                <span class="category">Cultural</span>

                <h3>Cultural Festival</h3>

                <p>📅 5 September 2026</p>

                <p>📍 Main Ground</p>

                <p>
                    Celebrate culture, music, dance
                    and creativity.
                </p>

                <a href="#" class="card-btn">View Details</a>

            </div>

        </div>


        <!-- Event 3 -->

        <div class="event-card">

            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS8JCWEZSTLB1ESteE3LkIKKqOCkdp1twKWWgzdCa4T92oLEMud1HJ8_A&s=10" alt="Career Seminar">

            <div class="event-info">

                <span class="category">Education</span>

                <h3>Career Guidance Seminar</h3>

                <p>📅 12 September 2026</p>

                <p>📍 Seminar Hall</p>

                <p>
                    Learn from experts and discover
                    new career opportunities.
                </p>

                <a href="#" class="card-btn">View Details</a>

            </div>

        </div>


        <!-- Event 4 -->

        <div class="event-card">

            <img src="https://www.chennaieventphotography.com/assets/image/detail/cricket-event-photography.webp" alt="Sports Championship">

            <div class="event-info">

                <span class="category">Sports</span>

                <h3>Sports Championship</h3>

                <p>📅 20 September 2026</p>

                <p>📍 Sports Ground</p>

                <p>
                    Compete, connect and showcase
                    your sporting talent.
                </p>

                <a href="#" class="card-btn">View Details</a>

            </div>

        </div>

    </div>

</section>


<!-- ================= ABOUT ================= -->

<section class="about" id="about">

    <div class="about-image">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSUOYJylQ87HdQHieqMOOOC904vTrrAJbJ4Aq2IhwK6VYHJt9FHTVuvQ5n6&s=10" alt="Event Management">
    </div>

    <div class="about-content">

        <p class="section-label">ABOUT US</p>

        <h2>
            We Make Event Management
            <span>Simple & Smart</span>
        </h2>

        <p>
            EventHub is an event management platform designed
            to make organizing and attending events simple.
        </p>

        <p>
            From event registration to digital tickets,
            attendance and certificates, everything can be
            managed from one platform.
        </p>

        <div class="about-features">

            <div>
                <strong>500+</strong>
                <span>Participants</span>
            </div>

            <div>
                <strong>50+</strong>
                <span>Events</span>
            </div>

            <div>
                <strong>20+</strong>
                <span>Organizers</span>
            </div>

        </div>

    </div>

</section>


<!-- ================= CONTACT ================= -->

<section class="contact" id="contact">

    <div class="section-title">

        <p>GET IN TOUCH</p>

        <h2>Contact Us</h2>

        <span>
            Have a question? We would love to hear from you.
        </span>

    </div>


    <div class="contact-container">

        <div class="contact-info">

            <h3>Let's Talk</h3>

            <p>
                Contact us for event information,
                registration help or any other queries.
            </p>

            <div class="contact-item">
                📧 eventhub@gmail.com
            </div>

            <div class="contact-item">
                📞 +91 98765 43210
            </div>

            <div class="contact-item">
                📍 Mumbai, Maharashtra
            </div>

        </div>


        <form class="contact-form">

            <input type="text" placeholder="Your Name">

            <input type="email" placeholder="Your Email">

            <input type="text" placeholder="Subject">

            <textarea rows="5" placeholder="Your Message"></textarea>

            <button type="submit">
                Send Message
            </button>

        </form>

    </div>

</section>


<!-- ================= FOOTER ================= -->

<footer>

    <div class="footer-content">

        <div>
            <h2>Event<span>Hub</span></h2>

            <p>
                Making events simple, smart
                and memorable.
            </p>
        </div>


        <div>
            <h3>Quick Links</h3>

            <a href="#home">Home</a>
            <a href="#events">Events</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </div>


        <div>
            <h3>Follow Us</h3>

            <p>Facebook</p>
            <p>Instagram</p>
            <p>LinkedIn</p>
        </div>

    </div>


    <div class="copyright">

        ©    2026 EventHub | Event Management System

    </div>

</footer>

</body>
</html>