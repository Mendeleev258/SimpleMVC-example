# UTF-8 Encoding Implementation

This document describes the UTF-8 encoding implementation across the SimpleMVC project to ensure proper handling of Russian and other multilingual characters.

## Database Configuration

The database is configured to use UTF-8 encoding:

- Connection string includes `charset=utf8mb4`
- PDO initialization command sets `SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci`
- All text fields use `CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`
- Tables use `DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci`
- Database host in Docker environment is `db` (not localhost)
- Database name in Docker environment is `smvcbase_in_docker`
- Database user in Docker environment is `myuser` with password `12345`

## PHP Configuration

UTF-8 encoding is configured at the application level:

- `mb_internal_encoding('UTF-8')` - Sets default encoding for string operations
- `mb_http_output('UTF-8')` - Sets HTTP output encoding
- `mb_regex_encoding('UTF-8')` - Sets regex encoding
- `setlocale(LC_ALL, 'ru_RU.UTF-8')` - Sets locale for Russian language support

## HTML Configuration

The HTML output is configured with UTF-8:

- Meta tag: `<meta http-equiv="content-type" content="text/html; charset=utf-8" />`
- Ensures browsers interpret content as UTF-8

## String Handling

All user-generated content is properly handled with UTF-8:

- `htmlspecialchars()` calls include UTF-8 encoding parameter: `htmlspecialchars($string, ENT_QUOTES, 'UTF-8')`
- Multibyte string functions use UTF-8: `mb_substr()`, `mb_strlen()` with 'UTF-8' parameter
- Removed inconsistent `mb_convert_encoding()` calls with 'auto' parameter

## Additional Configuration

To ensure proper UTF-8 handling throughout the stack:

- Apache is configured with `AddDefaultCharset utf-8` in the virtual host
- PHP bootstrap sets the content type header: `header('Content-Type: text/html; charset=utf-8')`
- PDO connection options include proper error handling and fetch modes

## File Encoding

All PHP files are saved in UTF-8 encoding without BOM to ensure consistent character handling across the application.

## Best Practices

When adding new code that handles text content:

1. Always use UTF-8 encoding in `htmlspecialchars()` calls
2. Use multibyte functions (`mb_*`) for string operations that might contain non-ASCII characters
3. Ensure database fields are properly configured with UTF-8 collation
4. Set proper HTTP headers for UTF-8 content when needed