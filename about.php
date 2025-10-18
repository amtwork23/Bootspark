<?php
session_start();
?>
<?php include 'header.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>About Bootspark</h1>
        <p>Discover the story behind our passion for premium men's footwear</p>
    </div>
</section>

<!-- About Content -->
<section class="about-content">
    <div class="container">
        <div class="about-grid">
            <div class="about-text">
                <h2>Our Story</h2>
                <p>Founded in 2020, Bootspark emerged from a simple belief: every man deserves shoes that combine exceptional quality, timeless style, and unmatched comfort. What started as a small family business has grown into a trusted destination for discerning gentlemen who value both form and function.</p>
                
                <p>We understand that shoes are more than just footwear – they're an expression of personality, a statement of confidence, and a foundation for every step you take in life. That's why we meticulously curate our collection, partnering only with the finest manufacturers who share our commitment to excellence.</p>
                
                <h3>Our Mission</h3>
                <p>To provide premium men's footwear that seamlessly blends style, comfort, and durability, ensuring every customer steps out with confidence and sophistication.</p>
                
                <h3>Our Values</h3>
                <ul class="values-list">
                    <li><strong>Quality First:</strong> We never compromise on materials or craftsmanship</li>
                    <li><strong>Customer Focus:</strong> Your satisfaction is our top priority</li>
                    <li><strong>Style & Comfort:</strong> Every shoe must excel in both areas</li>
                    <li><strong>Authenticity:</strong> We believe in honest business practices</li>
                </ul>
            </div>
            
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Our Store">
            </div>
        </div>
        
        <!-- Features Section -->
        <div class="features-section">
            <h2>Why Choose Bootspark?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">👞</div>
                    <h3>Premium Materials</h3>
                    <p>We use only the finest leathers and materials sourced from trusted suppliers worldwide.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Perfect Fit</h3>
                    <p>Our detailed size guides and expert recommendations ensure the perfect fit every time.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚚</div>
                    <h3>Fast Shipping</h3>
                    <p>Quick and secure delivery to your doorstep with tracking and insurance included.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💎</div>
                    <h3>Lifetime Support</h3>
                    <p>Our customer service team is here to help with any questions or concerns.</p>
                </div>
            </div>
        </div>
        
        <!-- Team Section -->
        <div class="team-section">
            <h2>Meet Our Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-1.2.1&auto=format&fit=crop&w=300&q=80" alt="John Smith">
                    <h3>John Smith</h3>
                    <p>Founder & CEO</p>
                </div>
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-1.2.1&auto=format&fit=crop&w=300&q=80" alt="Sarah Johnson">
                    <h3>Sarah Johnson</h3>
                    <p>Head of Design</p>
                </div>
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=crop&w=300&q=80" alt="Mike Davis">
                    <h3>Mike Davis</h3>
                    <p>Quality Assurance</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80');
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
    
    /* About Content */
    .about-content {
        padding: 4rem 0;
    }
    
    .about-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 4rem;
        margin-bottom: 4rem;
        align-items: start;
    }
    
    .about-text h2 {
        color: var(--dark);
        margin-bottom: 1.5rem;
        font-size: 2rem;
    }
    
    .about-text h3 {
        color: var(--dark);
        margin: 2rem 0 1rem;
        font-size: 1.5rem;
    }
    
    .about-text p {
        margin-bottom: 1.5rem;
        line-height: 1.8;
        color: #666;
    }
    
    .values-list {
        list-style: none;
        padding: 0;
    }
    
    .values-list li {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
        position: relative;
        color: #666;
    }
    
    .values-list li:before {
        content: "✓";
        position: absolute;
        left: 0;
        color: var(--secondary);
        font-weight: bold;
    }
    
    .about-image img {
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    /* Features Section */
    .features-section {
        background: #f8f9fa;
        padding: 4rem 0;
        margin: 4rem 0;
    }
    
    .features-section h2 {
        text-align: center;
        margin-bottom: 3rem;
        color: var(--dark);
        font-size: 2rem;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }
    
    .feature-card {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
    }
    
    .feature-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .feature-card h3 {
        color: var(--dark);
        margin-bottom: 1rem;
    }
    
    .feature-card p {
        color: #666;
        line-height: 1.6;
    }
    
    /* Team Section */
    .team-section {
        padding: 4rem 0;
    }
    
    .team-section h2 {
        text-align: center;
        margin-bottom: 3rem;
        color: var(--dark);
        font-size: 2rem;
    }
    
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }
    
    .team-member {
        text-align: center;
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .team-member img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 1rem;
    }
    
    .team-member h3 {
        color: var(--dark);
        margin-bottom: 0.5rem;
    }
    
    .team-member p {
        color: var(--secondary);
        font-weight: 500;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .about-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .hero h1 {
            font-size: 2rem;
        }
        
        .hero p {
            font-size: 1rem;
        }
    }
</style>

<?php include 'footer.php'; ?>
