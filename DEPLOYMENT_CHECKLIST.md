# Production Deployment Checklist

## 📋 Pre-Deployment Verification

### بخش 1: Environment Configuration

#### Application Settings
- [ ] `APP_ENV` بر روی `production` تنظیم شده باشد
- [ ] `APP_DEBUG` بر روی `false` تنظیم شده باشد
- [ ] `APP_KEY` بر روی یک مقدار تصادفی قوی تنظیم شده باشد (بدون "changeme")
- [ ] `APP_URL` بر روی domain واقعی تنظیم شده باشد (بدون localhost)
- [ ] `APP_TRUST_PROXY` بر اساس setup (false اگر direct connection، true اگر behind reverse proxy)

#### Security Configuration
- [ ] `HEALTH_TOKEN` یک مقدار تصادفی قوی است
- [ ] `HEALTH_ALLOWED_CIDRS` برای monitoring IPs محدود شده است
- [ ] JWT/Token secrets strong و secure هستند
- [ ] کلیدهای رمزنگاری strong هستند

#### Database Configuration
- [ ] Database user privileges: فقط SELECT, INSERT, UPDATE, DELETE (بدون CREATE, ALTER, DROP)
- [ ] Database password: strong و secure
- [ ] Database connection: SSL/TLS enabled (if available)
- [ ] Backup strategy: automated و tested

### بخش 2: File Permissions و Directory Structure

#### Directory Permissions
```
storage/          → 755 (app-user readable/writable)
storage/logs/     → 755 (app-user writable)
storage/cache/    → 755 (app-user writable)
public/           → 755 (world readable)
config/           → 700 (app-user only)
.env              → 600 (app-user only)
```

- [ ] Storage directory NOT 777 (world-writable)
- [ ] Logs directory writable by app process only
- [ ] Config files NOT readable by web server
- [ ] .env file NOT in web root
- [ ] Permissions verified with `ls -la`

#### File Ownership
- [ ] App files owned by app user (not root)
- [ ] Web server process runs as unprivileged user
- [ ] Database files owned by database user
- [ ] Backup files owned securely

### بخش 3: SSL/TLS Configuration

- [ ] HTTPS enabled on all endpoints
- [ ] Valid SSL certificate installed
- [ ] Certificate from trusted CA (not self-signed in production)
- [ ] Certificate not expired (check renewal date)
- [ ] SSL/TLS version: TLS 1.2+ only (disable SSLv3, TLS 1.0, 1.1)
- [ ] Ciphers: strong suites only (no weak ciphers)
- [ ] HTTP redirected to HTTPS (301 permanent)
- [ ] HSTS header enabled (Strict-Transport-Security)

### بخش 4: Security Headers Verification

Request a page and verify headers:
```bash
curl -I https://yourdomain.com
```

Required headers present:
- [ ] Content-Security-Policy
- [ ] X-Content-Type-Options: nosniff
- [ ] X-Frame-Options: SAMEORIGIN
- [ ] Strict-Transport-Security: max-age=31536000
- [ ] Referrer-Policy: strict-origin-when-cross-origin
- [ ] Cache-Control: appropriate for content type

### بخش 5: Authentication و Session

#### Password Policy
- [ ] Minimum 8 characters enforced
- [ ] Password requirements: uppercase, lowercase, numbers
- [ ] Password hashing: PASSWORD_DEFAULT (bcrypt)
- [ ] No password logging
- [ ] Old passwords never exposed in logs

#### Session Configuration
- [ ] Session timeout: 30 minutes (or configurable)
- [ ] Session cookies: HttpOnly, Secure, SameSite=Lax
- [ ] Session storage: secure backend (not files in /tmp)
- [ ] Session regeneration after login
- [ ] Session invalidation on logout

#### Rate Limiting
- [ ] Login endpoint: 5 attempts per 15 minutes per IP
- [ ] Register endpoint: 3 attempts per hour per IP
- [ ] API endpoints: configured per requirements
- [ ] Rate limit headers returned in responses

### بخش 6: API Security

- [ ] Bearer token auth required for protected endpoints
- [ ] Token expiration: access (1 hour) + refresh (7 days)
- [ ] CORS configured restrictively
- [ ] API versioning implemented (if needed)
- [ ] API responses: no sensitive data in errors
- [ ] API documentation: security warnings included

### بخش 7: Admin Panel Access

- [ ] Admin routes require authentication
- [ ] Admin routes require admin role/permission
- [ ] Resource ownership enforced (can't edit others' resources)
- [ ] Audit logging enabled for admin actions
- [ ] Only specific IPs allowed (if configurable)
- [ ] Admin timeout shorter than regular (if applicable)

### بخش 8: Database Security

- [ ] Database not directly accessible from internet
- [ ] Database behind firewall
- [ ] Connection string NOT in public repo
- [ ] Database backups automated
- [ ] Backups encrypted
- [ ] Backup restoration tested
- [ ] Old backups retained for X days
- [ ] Database size monitoring enabled

### بخش 9: Logging و Monitoring

#### Log Configuration
- [ ] Logging enabled for all security events
- [ ] Log rotation configured (prevent disk full)
- [ ] Log files NOT accessible from web
- [ ] Sensitive data NOT logged (passwords, tokens)
- [ ] Log retention: X days (configure based on compliance)

#### Monitoring Enabled
- [ ] Health endpoint monitored (uptime monitoring)
- [ ] Error rate monitoring
- [ ] Authentication failure tracking
- [ ] Authorization failure tracking
- [ ] Performance monitoring (response times)
- [ ] Disk space monitoring
- [ ] Database connection monitoring
- [ ] Memory usage monitoring

#### Alert Configuration
- [ ] Alerts for high error rates
- [ ] Alerts for authentication failures
- [ ] Alerts for health endpoint down
- [ ] Alerts for disk space issues
- [ ] Contact list for on-call response

### بخش 10: Backup و Disaster Recovery

- [ ] Automated daily database backups
- [ ] Backup restoration procedure tested
- [ ] Backups stored off-site (not on same server)
- [ ] Backup encryption enabled
- [ ] Backup integrity verification
- [ ] Recovery time objective (RTO) documented
- [ ] Recovery point objective (RPO) documented
- [ ] Disaster recovery plan documented
- [ ] Disaster recovery plan tested

### بخش 11: Firewall و Network

- [ ] Web server port 80 (HTTP) open to internet
- [ ] Web server port 443 (HTTPS) open to internet
- [ ] Database port NOT open to internet
- [ ] SSH port on non-standard port (if SSH needed)
- [ ] SSH key-based auth only (no password)
- [ ] SSH rate limiting enabled
- [ ] DDoS protection enabled (Cloudflare, etc.)
- [ ] WAF rules configured
- [ ] Outbound connections filtered (if applicable)

### بخش 12: Dependency Management

```bash
# Check for outdated packages
composer outdated

# Check for security vulnerabilities
composer audit
```

- [ ] All dependencies up-to-date
- [ ] No known security vulnerabilities
- [ ] Composer lock file committed
- [ ] Security patches monitored (e.g., Github alerts)
- [ ] Dependency scanning tool configured

### بخش 13: Secrets Management

- [ ] API keys NOT in version control
- [ ] API keys NOT in code
- [ ] API keys NOT in logs
- [ ] Secrets stored in environment variables
- [ ] Secrets rotated regularly
- [ ] Access to secrets restricted
- [ ] Secrets audit trail maintained

### بخش 14: Code Deployment

#### Pre-deployment Checks
- [ ] All tests passing
- [ ] Code review completed
- [ ] Security review completed
- [ ] No debug code in deployment
- [ ] No development endpoints exposed
- [ ] No test data in production

#### Deployment Process
- [ ] Deploy to staging first
- [ ] Verify on staging
- [ ] Backup production before deploy
- [ ] Zero-downtime deployment (if applicable)
- [ ] Health checks after deploy
- [ ] Rollback plan ready

### بخش 15: Post-Deployment Verification

```bash
# Verify environment
curl -I https://yourdomain.com/api/health

# Check logs
tail -f storage/logs/php-errors.log

# Verify auth works
# Test login/logout flow manually

# Test admin panel
# Verify only admins can access

# Test rate limiting
# Make multiple requests to verify limits
```

- [ ] Application loads successfully
- [ ] Health endpoint returns 200 OK
- [ ] Login page loads
- [ ] Login/logout works
- [ ] Admin panel accessible only to admins
- [ ] API endpoints responding
- [ ] Logs show no errors
- [ ] Security headers present
- [ ] HTTPS working correctly
- [ ] Redirects working (HTTP → HTTPS)

### بخش 16: Monitoring Dashboard Setup

- [ ] Health checks configured (external)
- [ ] Error tracking enabled (e.g., Sentry)
- [ ] Performance monitoring enabled
- [ ] Uptime monitoring enabled
- [ ] Alert notifications configured
- [ ] Dashboards created for team

### بخش 17: Documentation

- [ ] Deployment guide written
- [ ] Configuration documented
- [ ] Admin guide written
- [ ] API documentation complete
- [ ] Security procedures documented
- [ ] Incident response plan written
- [ ] On-call procedures documented

### بخش 18: Team Training

- [ ] Team trained on deployment procedure
- [ ] Team trained on incident response
- [ ] Team trained on security procedures
- [ ] Team trained on monitoring dashboards
- [ ] On-call rotation established

---

## 🔍 Production Monitoring (Ongoing)

### Daily Tasks
- [ ] Check health endpoint (automated)
- [ ] Review error logs for errors/warnings
- [ ] Verify uptime monitoring alerts working
- [ ] Check disk space usage

### Weekly Tasks
- [ ] Review authentication failures
- [ ] Review authorization failures
- [ ] Check backup integrity
- [ ] Review performance metrics
- [ ] Update dependency vulnerability list

### Monthly Tasks
- [ ] Update all dependencies
- [ ] Review access logs for anomalies
- [ ] Rotate API tokens
- [ ] Review admin activity
- [ ] Test backup restoration

### Quarterly Tasks
- [ ] Security audit review
- [ ] Penetration testing (if budget allows)
- [ ] Compliance review
- [ ] Disaster recovery drill
- [ ] Password rotation (admin accounts)

---

## 🚨 Incident Response

### If Issues Detected

1. **Immediate**
   - [ ] Check health endpoint status
   - [ ] Check error logs
   - [ ] Check uptime monitoring alerts
   - [ ] Check disk space

2. **Assessment**
   - [ ] Determine issue severity
   - [ ] Check if data compromised
   - [ ] Check if service down
   - [ ] Identify root cause

3. **Notification**
   - [ ] Alert on-call team
   - [ ] Notify stakeholders (if data breach)
   - [ ] Document incident
   - [ ] Begin recovery

4. **Recovery**
   - [ ] Apply fix / rollback
   - [ ] Restore from backup (if needed)
   - [ ] Verify service
   - [ ] Monitor closely

5. **Post-Incident**
   - [ ] Root cause analysis
   - [ ] Implement preventive measures
   - [ ] Update runbooks
   - [ ] Team debrief

---

## 📊 Deployment Status

- **Status**: ⬜ Not Started
- **Progress**: 0/100+ items
- **Estimated Completion**: __/__/____
- **Owner**: _________________
- **Last Reviewed**: _________________

---

**Last Updated**: 2026-09-01
**Version**: 1.0
**Framework**: Custom PHP Framework (Cyron)
