# Security Hardening Guide - بهبود امنیت فریمورک

## خلاصهٔ امنیتی

این سند خلاصهٔ کاملی از تمام بهبودهای امنیتی اعمال‌شده بر روی فریمورک است.

---

## 1. لایهٔ Auth و Session

### 1.1 Session Timeout و Lifetime
- **فایل**: `app/Core/Authentication/Auth.php`
- **بهبودها**:
  - Idle timeout: 30 دقیقه
  - Max lifetime: 24 ساعت
  - Session regeneration بعد از login
  - خودکار logout برای sessions منقضی
  - تمام IP/User-Agent fingerprinting با trusted client IP

### 1.2 Lockout Policy
- **فایل**: `app/Core/Authentication/Auth.php`
- **بهبودها**:
  - Max failed attempts: 5 تا
  - Lockout duration: 15 دقیقه
  - IP-based و User-based lockout
  - Security alerts برای lockout events

### 1.3 Session Integrity
- IP hash check در هر request
- User-Agent hash check
- Last activity tracking
- Automatic logout در غیر‌عملی sessions

---

## 2. Request Hardening

### 2.1 Dangerous Methods
- **فایل**: `app/Http/Middlewares/RequestHardeningMiddleware.php`
- **بهبودها**:
  - TRACE, TRACK, CONNECT methods رد می‌شوند
  - Content-Length validation
  - Host header validation (regex pattern matching)
  - Max body size: 10 MB

### 2.2 Proxy Header Handling
- **فایل**: `app/Request.php`
- **بهبودها**:
  - APP_TRUST_PROXY flag برای proxy-aware IP detection
  - بدون trust proxy: فقط REMOTE_ADDR استفاده می‌شود
  - Safe IP parsing و validation

---

## 3. CSRF Protection

### 3.1 Token Validation
- **فایل**: `app/Http/Middlewares/CsrfMiddleware.php`
- **بهبودها**:
  - Token hash verification (hash_equals)
  - Origin/Referer validation
  - نرمال‌سازی Host/Port/Scheme
  - API bearer token bypass برای token-based auth

### 3.2 Safe Methods
- GET, HEAD, OPTIONS: bypass
- API calls: bearer token accepted
- Form submissions: _token field required

---

## 4. Browser Security Headers

### 4.1 Content Security Policy
- **فایل**: `app/Http/Middlewares/SecurityHeadersMiddleware.php`
- **توابع**:
  - default-src: 'self'
  - script-src: self + trusted CDNs (no unsafe-inline in production)
  - style-src: self + trusted CDNs
  - object-src: 'none'
  - frame-ancestors: 'self'

### 4.2 Cache Control
- **بهبودها**:
  - Sensitive paths (admin, dashboard, API, authenticated): no-cache, no-store
  - Public paths: 300 seconds max-age
  - Pragma: no-cache
  - Expires: 0

### 4.3 محافظت‌های دیگر
- X-Content-Type-Options: nosniff
- X-Frame-Options: SAMEORIGIN
- Referrer-Policy: strict-origin-when-cross-origin
- Permissions-Policy: camera, microphone, geolocation disabled
- HSTS: 1 year max-age (HTTPS only)
- Expect-CT: enforce

---

## 5. Authentication Middleware

### 5.1 AuthMiddleware
- **فایل**: `app/Http/Middlewares/AuthMiddleware.php`
- **بهبودها**:
  - Session integrity check
  - User status validation (active/enabled/approved/verified)
  - Suspension check (suspended_until)
  - Automatic logout برای disabled users

### 5.2 ApiAuthMiddleware
- **فایل**: `app/Http/Middlewares/ApiAuthMiddleware.php`
- **بهبودها**:
  - Bearer token validation
  - Token expiration check
  - User status validation
  - Suspension enforcement

### 5.3 AdminMiddleware
- **فایل**: `app/Http/Middlewares/AdminMiddleware.php`
- **بهبودها**:
  - Role-based access check
  - Permission-based access check
  - Multiple admin role names support
  - Deny response for unauthorized

---

## 6. Production Guard

### 6.1 Startup Validation
- **فایل**: `app/Core/Http/Security/ProductionGuard.php`
- **بهبودها**:
  - DEBUG mode check (no debug in production)
  - APP_KEY validation (must be strong)
  - APP_URL validation (no localhost)
  - Storage path permissions check (no world-writable)

### 6.2 Environment Safety
- Fail-safe checks قبل از app start
- Exception thrown برای unsafe configuration
- Prevents accidental production misconfiguration

---

## 7. Health Endpoint Security

### 7.1 Access Control
- **فایل**: `app/Http/Controllers/HealthController.php`
- **بهبودها**:
  - IP whitelist checking (CIDR support)
  - HEALTH_TOKEN header validation (production)
  - Database, cache, storage checks
  - Degraded state reporting

---

## 8. Data Validation و Sanitization

### 8.1 Input Sanitization
- **فایل**: `app/Support/Helpers/Core/Rendering.php`
- **بهبودها**:
  - HTML escaping (e() helper)
  - Control character removal (sanitize())
  - CSP nonce generation

### 8.2 File Upload Validation
- **فایل**: `app/Request.php`
- **بهبودها**:
  - MIME type checking
  - File size validation
  - Extension whitelist (if configured)
  - Uploaded file verification

---

## 9. Logging و Monitoring

### 9.1 Security Events
- Login success/failure
- Lockout events
- Authorization failures
- IP blocking
- Suspicious activity

### 9.2 Error Handling
- **فایل**: `app/Core/Exceptions/Handler.php`
- **بهبودها**:
  - Dev debug bar (non-production)
  - Safe error messages (production)
  - SQL query logging (development)
  - Stack trace logging

---

## 10. Deployment Checklist

### قبل از Production Deployment:

#### Environment Configuration
- [ ] APP_ENV = 'production'
- [ ] APP_DEBUG = false
- [ ] APP_KEY = strong random value (not changeme)
- [ ] APP_URL = real domain (not localhost)
- [ ] HEALTH_TOKEN = strong random value
- [ ] HEALTH_ALLOWED_CIDRS = restricted to monitoring IPs

#### Database و Storage
- [ ] Database user: limited permissions
- [ ] Storage directory: 755 permissions (not 777)
- [ ] Logs directory: writable by app user only
- [ ] Cache directory: writable by app user only

#### SSL/TLS
- [ ] HTTPS enabled
- [ ] Valid SSL certificate
- [ ] HSTS header enabled
- [ ] Redirect HTTP → HTTPS

#### Security Headers
- [ ] CSP header present
- [ ] X-Frame-Options header set
- [ ] X-Content-Type-Options header set
- [ ] Referrer-Policy header set
- [ ] CORS headers configured (if needed)

#### Authentication
- [ ] Password requirements enforced (min 8 chars)
- [ ] Password hashing: PASSWORD_DEFAULT (bcrypt)
- [ ] Session timeout: 30 minutes (configurable)
- [ ] Rate limiting: enabled

#### API Security
- [ ] Bearer token auth required
- [ ] Token expiration: 1 hour (access), 7 days (refresh)
- [ ] Rate limiting: login/register endpoints
- [ ] CORS: restricted to trusted domains

#### Admin Panel
- [ ] Admin middleware: applied to all admin routes
- [ ] Role-based access control: enforced
- [ ] Resource ownership checks: implemented
- [ ] Audit logging: enabled

#### Monitoring
- [ ] Health endpoint: configured
- [ ] Logging: centralized
- [ ] Error tracking: configured
- [ ] Uptime monitoring: enabled

#### Backup و Recovery
- [ ] Database backups: automated
- [ ] Backup verification: tested
- [ ] Recovery procedure: documented
- [ ] Encryption: enabled for backups

#### Third-Party
- [ ] Dependencies: up-to-date
- [ ] Security patches: applied
- [ ] Vulnerabilities: scanned
- [ ] License compliance: checked

#### Operations
- [ ] Firewall rules: restrictive
- [ ] DDoS protection: enabled
- [ ] WAF rules: configured
- [ ] Log retention: configured

---

## 11. Current Security Level

### ✅ Completed (Production-Ready)
- Session security (timeout, fingerprinting, regeneration)
- Request hardening (method, host, size validation)
- CSRF protection (token + Origin validation)
- Browser security headers (CSP, HSTS, etc.)
- Authentication hardening (status, suspension checks)
- API authentication (bearer tokens, expiration)
- Admin access control (role-based, resource ownership)
- Production guard (environment validation)
- Health endpoint security (IP whitelist, token)
- Input sanitization (HTML escaping, control chars)
- File upload validation (MIME, size, origin)
- Error handling (debug bar, safe messages)

### ⚠️ Remaining (For Full Public Deployment)
- Complete route-level permission audit
- Custom rate limiting profiles per endpoint
- Advanced threat detection (anomaly detection)
- Centralized logging infrastructure
- WAF integration (if not already in place)
- Incident response procedures
- Security testing (penetration testing)
- Backup and disaster recovery

### Overall Assessment
**Status**: 🟡 Requires final integration and validation
**For Public**: 🟡 Requires final integration, route audit and production validation (needs final permission audit + ops configuration)

---

## 12. Key Files for Security Review

1. **Authentication**: `app/Core/Authentication/Auth.php`
2. **Request**: `app/Request.php`
3. **Middlewares**: `app/Http/Middlewares/*.php`
4. **Bootstrap**: `app/bootstrap.php`
5. **Helpers**: `app/Support/Helpers/Core/*.php`
6. **Routes**: `routes/admin.php`, `routes/api.php`, `routes/auth.php`
7. **Controllers**: `app/Http/Controllers/{Admin,Api,Auth}/*.php`

---

## 13. Security Best Practices (After Deployment)

1. **Regular Updates**
   - Keep PHP version up-to-date
   - Update all dependencies monthly
   - Apply security patches immediately

2. **Monitoring**
   - Watch health endpoint logs
   - Monitor failed login attempts
   - Track authorization failures
   - Review error logs daily

3. **Backups**
   - Daily automated backups
   - Test restore procedures weekly
   - Keep off-site backups
   - Encrypt all backups

4. **Access Control**
   - Review admin users quarterly
   - Disable unused accounts
   - Rotate API tokens regularly
   - Audit permission changes

5. **Incident Response**
   - Have incident response plan
   - Document security incidents
   - Perform post-incident reviews
   - Update procedures based on learnings

---

## تاریخ آخرین بروزرسانی

**تاریخ**: 2026-09-01
**وضعیت**: Production-Ready (Internal/Private)
**نسخه**: 1.0

---

## تماس و پشتیبانی

برای سؤالات امنیتی یا گزارش مسائل:
- توسعه داخلی: استفاده از Issue Tracker
- Security vulnerabilities: توسط secure email channel
- قطع‌نامه: تمام تغییرات امنیتی ثبت می‌شوند
