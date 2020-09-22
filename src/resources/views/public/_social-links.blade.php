<div class="social-links">
    <ul class="social-links-list">
        <li class="social-links-item">
            <a class="social-links-link social-links-link-facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" rel="noopener noreferrer">
                <span class="fa fa-fw fa-facebook-official"></span>
            </a>
        </li>
        <li class="social-links-item">
            <a class="social-links-link social-links-link-twitter" href="https://twitter.com/intent/tweet?text={{ $model->title }}&url={{ url()->current() }}" target="_blank" rel="noopener noreferrer">
                <span class="fa fa-fw fa-twitter"></span>
            </a>
        </li>
        <li class="social-links-item">
            <a class="social-links-link social-links-link-linkedin" href="https://www.linkedin.com/sharing/share-offsite/?url={{ url()->current() }}" target="_blank" rel="noopener noreferrer">
                <span class="fa fa-fw fa-linkedin"></span>
            </a>
        </li>
        <li class="social-links-item">
            <a class="social-links-link social-links-link-whatsapp" href="https://wa.me/?text={{ url()->current() }}" target="_blank" rel="noopener noreferrer">
                <span class="fa fa-fw fa-whatsapp"></span>
            </a>
        </li>
        <li class="social-links-item">
            <a class="social-links-link social-links-link-mail" href="mailto:?subject={{ $model->title }}&body={{ url()->current() }}" target="_blank" rel="noopener noreferrer">
                <span class="fa fa-fw fa-envelope"></span>
            </a>
        </li>
    </ul>
</div>
