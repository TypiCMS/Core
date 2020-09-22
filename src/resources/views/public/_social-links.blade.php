<div class="social-links">
    <ul class="social-links-list">
        <li class="social-links-item">
            <a class="social-links-link social-links-facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" rel="noopener noreferrer">
                <span class="social-links-icon social-links-facebook-icon"></span>
            </a>
        </li>
        <li class="social-links-item">
            <a class="social-links-link social-links-twitter" href="https://twitter.com/intent/tweet?text={{ $model->title }}&url={{ url()->current() }}" target="_blank" rel="noopener noreferrer">
                <span class="social-links-icon social-links-twitter-icon"></span>
            </a>
        </li>
        <li class="social-links-item">
            <a class="social-links-link social-links-linkedin" href="https://www.linkedin.com/sharing/share-offsite/?url={{ url()->current() }}" target="_blank" rel="noopener noreferrer">
                <span class="social-links-icon social-links-linkedin-icon"></span>
            </a>
        </li>
        <li class="social-links-item">
            <a class="social-links-link social-links-whatsapp" href="https://wa.me/?text={{ url()->current() }}" target="_blank" rel="noopener noreferrer">
                <span class="social-links-icon social-links-whatsapp-icon"></span>
            </a>
        </li>
        <li class="social-links-item">
            <a class="social-links-link social-links-mail" href="mailto:?subject={{ $model->title }}&body={{ url()->current() }}" target="_blank" rel="noopener noreferrer">
                <span class="social-links-icon social-links-mail-icon"></span>
            </a>
        </li>
    </ul>
</div>
