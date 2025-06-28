
</main>
<footer class="footer">
    <div class="container">
        <div class="footer-main-content">
            <div class="footer-section footer-contact">
                <h4>Contact</h4>
                <p><?php echo $contact_name; ?><br>
                   <?php echo $contact_address; ?></p>
                <p>E-Mail: <a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a><br>
                   WhatsApp: <a href="https://wa.me/<?php echo $contact_phone_raw; ?>" target="_blank" rel="noopener noreferrer"><?php echo $contact_phone_formatted; ?></a></p>
            </div>

            <div class="footer-section footer-business">
                <h4>Business</h4>
                <p><?php echo $business_name; ?><br>
                   KVK: <?php echo $business_kvk; ?><br>
                   btw-id: <?php echo $business_btw; ?></p>
            </div>

            <div class="footer-section footer-reviews">
                <h4>Reviews</h4>
                <p>Feeling generous? ❤️<br>
                   <a href="<?php echo $review_link_google; ?>" target="_blank" rel="noopener noreferrer">Leave a review on Google</a><br>
                   <a href="<?php echo $review_link_trustpilot; ?>" target="_blank" rel="noopener noreferrer">Leave a review on Trustpilot</a></p>
                <p style="margin-top: 1rem;">Curious about experiences?<br>
                   <a href="<?php echo $reviews_link_trustpilot; ?>" target="_blank" rel="noopener noreferrer">Read reviews on Trustpilot ✨</a></p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>
                <a href="/terms.php" style="color: #9ca3af; text-decoration: underline;">Terms of Service</a> |
                <a href="/privacy-policy.php" style="color: #9ca3af; text-decoration: underline;">Privacy Policy</a>
            </p>
            <p>© <span id="current-year"></span> <?php echo $site_name; ?>. All Rights Reserved.</p>
            <p class="disclaimer"><?php echo $site_disclaimer; ?></p>
        </div>
    </div>
</footer>

<script src="/js/script.js"></script>
</body>
</html>
