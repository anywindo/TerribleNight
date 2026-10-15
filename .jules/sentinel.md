## 2024-05-18 - Prevent Path Traversal in Storage Route
**Vulnerability:** The `/storage/{path}` endpoint in `routes/web.php` took a path directly from the URL and used it in `public_path($path)` without sanitization. An attacker could use `../` to access files outside the intended directory (e.g., `../../.env`).
**Learning:** Even custom routes built for serving files need explicit path sanitization. Laravel router doesn't automatically block `..` sequences within custom `{path}` parameters that accept `.*`.
**Prevention:** Always validate and sanitize user-provided paths before passing them to filesystem functions. A simple string check for `..` is a quick defense, but using `realpath()` and comparing it against the intended base directory is more robust.
