<?php
session_start();

$message = '';
$error = '';

if($_POST) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message_text = $_POST['message'] ?? '';
    
    if(empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $error = "Please fill in all fields.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // In a real application, you would send an email or save to database
        $message = "Thank you for your message! We'll get back to you within 24 hours.";
    }
}
?>
<?php include 'header.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
    </div>
</section>

<!-- Contact Content -->
<section class="contact-content">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form-section">
                <h2>Send us a Message</h2>
                
                <?php if($message): ?>
                    <div class="success-message"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="contact-form">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="6" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div class="contact-info-section">
                <h2>Get in Touch</h2>
                <p>We're here to help and answer any question you might have. We look forward to hearing from you!</p>
                
                <div class="contact-details">
                    <div class="contact-item">
                        <div class="contact-icon">📍</div>
                        <div class="contact-text">
                            <h3>Address</h3>
                            <p>123 Shoe Street<br>Kalamassery, Kerala<br>India</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">📞</div>
                        <div class="contact-text">
                            <h3>Phone</h3>
                            <p>+91 8394848982<br>Mon-Fri: 9AM-6PM EST</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">✉️</div>
                        <div class="contact-text">
                            <h3>Email</h3>
                            <p>support@bootspark.com<br>info@bootspark.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">⏰</div>
                        <div class="contact-text">
                            <h3>Business Hours</h3>
                            <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
                        </div>
                    </div>
                </div>
                
                <!-- Social Media -->
                <div class="social-section">
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="https://www.facebook.com/bootspark" class="social-link">Facebook</a>
                        <a href="https://www.twitter.com/bootspark" class="social-link">Twitter</a>
                        <a href="https://www.instagram.com/bootspark" class="social-link">Instagram</a>
                        <a href="https://www.linkedin.com/bootspark" class="social-link">LinkedIn</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="faq-section">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>What is your return policy?</h3>
                    <p>We offer a 30-day return policy for all unworn items in their original packaging. Returns are free and easy.</p>
                </div>
                <div class="faq-item">
                    <h3>How long does shipping take?</h3>
                    <p>Standard shipping takes 3-5 business days. Express shipping is available for next-day delivery.</p>
                </div>
                <div class="faq-item">
                    <h3>Do you offer international shipping?</h3>
                    <p>Yes, we ship to most countries worldwide. Shipping costs and delivery times vary by location.</p>
                </div>
                <div class="faq-item">
                    <h3>How do I find my correct shoe size?</h3>
                    <p>Use our detailed size guide on each product page, or contact our customer service for personalized assistance.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1423666639041-f56000c27a9a?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 5rem 0;
        text-align: center;
    }
    
    .hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .hero p {
        font-size: 1.2rem;
        max-width: 600px;
        margin: 0 auto;
    }
    
    /* Contact Content */
    .contact-content {
        padding: 4rem 0;
    }
    
    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        margin-bottom: 4rem;
    }
    
    /* Contact Form */
    .contact-form-section h2 {
        color: var(--dark);
        margin-bottom: 2rem;
        font-size: 2rem;
    }
    
    .contact-form {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--dark);
        font-weight: 500;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--secondary);
    }
    
    .submit-btn {
        background: var(--secondary);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 4px;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s;
        width: 100%;
    }
    
    .submit-btn:hover {
        background: #2980b9;
    }
    
    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
        border: 1px solid #c3e6cb;
    }
    
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
        border: 1px solid #f5c6cb;
    }
    
    /* Contact Info */
    .contact-info-section h2 {
        color: var(--dark);
        margin-bottom: 1rem;
        font-size: 2rem;
    }
    
    .contact-info-section p {
        color: #666;
        margin-bottom: 2rem;
        line-height: 1.6;
    }
    
    .contact-details {
        margin-bottom: 3rem;
    }
    
    .contact-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 2rem;
    }
    
    .contact-icon {
        font-size: 1.5rem;
        margin-right: 1rem;
        margin-top: 0.2rem;
    }
    
    .contact-text h3 {
        color: var(--dark);
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }
    
    .contact-text p {
        color: #666;
        margin: 0;
        line-height: 1.5;
    }
    
    .social-section h3 {
        color: var(--dark);
        margin-bottom: 1rem;
    }
    
    .social-links {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .social-link {
        background: var(--secondary);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        text-decoration: none;
        transition: background 0.3s;
    }
    
    .social-link:hover {
        background: #2980b9;
    }
    
    /* FAQ Section */
    .faq-section {
        background: #f8f9fa;
        padding: 4rem 0;
        margin-top: 4rem;
    }
    
    .faq-section h2 {
        text-align: center;
        margin-bottom: 3rem;
        color: var(--dark);
        font-size: 2rem;
    }
    
    .faq-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .faq-item {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .faq-item h3 {
        color: var(--dark);
        margin-bottom: 1rem;
        font-size: 1.2rem;
    }
    
    .faq-item p {
        color: #666;
        line-height: 1.6;
        margin: 0;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .hero h1 {
            font-size: 2rem;
        }
        
        .hero p {
            font-size: 1rem;
        }
        
        .social-links {
            justify-content: center;
        }
    }
</style>

<?php include 'footer.php'; ?>
