 # Project Content & Structure

 This document describes the directory layout for static pages, blog content, and file uploads,
 providing a foundation for future CMS-driven workflows and GDPR-compliant file storage.

 ## 1. Static Page Templates (`src/pages/`)

 All site pages are stored under `src/pages/` in slug-based folders. Each page route corresponds to
 a subdirectory with an `index.html` file. For example:

 ```
 src/pages/
 ├─ index.html                     <-- Home page
 ├─ announcement.html              <-- Standalone page
 └─ blog/
     ├─ index.html                 <-- Blog listing page (formerly blog.html)
     ├─ feelings-flags/index.html
     ├─ tck-guide-netherlands/index.html
     ├─ tck-flourishing-abroad/index.html
     ├─ integration-checklist/index.html
     └─ integration-flashcards/index.html
 ```

 Each subfolder (e.g., `feelings-flags/`, `tck-guide-netherlands/`) contains its own `index.html`
 so that routes map cleanly to `/blog/<slug>/`.

 ## 2. Version-Controlled Blog Content (`content/blog/`)

 Blog posts authored and reviewed by the team live in `content/blog/default/`. Future user-
 or CMS-generated posts will be created under `content/blog/user/`, which is intended to be writable
 by the CMS:

 ```
 content/blog/
 ├─ default/    <-- Markdown or HTML source for built-in blog posts (versioned)
 └─ user/       <-- Empty folder; CMS will write new posts here
 ```

 ## 3. Static Assets (`assets/`)

 Default images, documents, and videos (placeholders for integrators) remain in the read-only
 `assets/` tree:

 ```
 assets/
 ├─ images/     <-- e.g. default-banner.png, default-hero.jpg, default-blog-banner.jpg
 ├─ docs/       <-- e.g. default-integration-checklist.pdf
 └─ vids/       <-- e.g. default-intro-video.mp4, default-intro-video.webm
 ```

 For content or posts served by users in the future, assets will not be committed here.

 ## 4. CMS Uploads & Writable Storage (`uploads/`)

 The `uploads/` folder is intended for writable, dynamic content (user uploads, CMS assets).
 In particular, blog post media will reside under:

 ```
 uploads/blog/
   └─ <post-slug>/
       ├─ hero.jpg
       ├─ attachment.pdf
       └─ ...
 ```

 ## 5. File-Permissions & GDPR Considerations

 - **Directory write access**: Ensure `content/blog/user/` and `uploads/` (e.g. `uploads/blog/`) are
   writable by the web or CMS process user (commonly via group permissions or ACLs).
 - **Storage isolation**: Keep dynamic uploads separate from static assets for security and audit.
 - **Data retention & deletion**: Implement deletion/retention policies when building the CMS.
 - **Metadata stripping**: Consider removing sensitive metadata (e.g. EXIF) from user-uploaded files.
 - **Audit logging**: Track who uploaded/deleted content and when (for compliance).

 ---

 _This file lays the groundwork for a future CMS integration and GDPR-safe hosting of user content._