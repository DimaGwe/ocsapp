# OCS Marketplace - Backup & Recovery Guide

**Generated**: 2025-11-20
**Backup Date**: November 20, 2025
**Backup Size**: 32 MB (compressed)

---

## 📦 BACKUP LOCATIONS

### Server-Side Backups
- **Location**: `/home/ubuntu/backups/` on AWS EC2
- **Latest Backup**: `ocs_marketplace_backup_20251120_202250.tar.gz`
- **Backup Script**: `/home/ubuntu/backup-marketplace.sh`
- **Automated**: Can be scheduled with cron

### Local Backups
- **Location**: `C:\xampp\ocsapp\backups\`
- **Full Backup Archive**: `ocs_marketplace_backup_20251120_202250.tar.gz` (32 MB)
- **Database Dump**: `marketplace_db_20251120.sql` (1.3 MB)
- **Environment File**: `marketplace.env`

### Backup Contents
```
backups/
├── ocs_marketplace_backup_20251120_202250.tar.gz    # Complete backup (32 MB)
├── ocs_marketplace_backup_20251120_202250/          # Extracted backup
│   ├── app_files.tar.gz           (15 MB)  - Application code
│   ├── database.sql.gz            (178 KB) - Database dump
│   ├── uploads.tar.gz             (7.6 MB) - Public uploads
│   └── storage_uploads.tar.gz     (9.3 MB) - Storage uploads
├── marketplace_db_20251120.sql                      # Raw database dump (1.3 MB)
└── marketplace.env                                  # Environment configuration
```

---

## 🔄 BACKUP STRATEGY

### Three-Tier Backup System

#### Tier 1: AWS RDS Automated Backups
- **Frequency**: Daily automated snapshots
- **Retention**: 7 days
- **Type**: Point-in-time recovery
- **What**: Database only
- **Access**: AWS RDS Console

#### Tier 2: Server-Side Backups
- **Frequency**: On-demand (can be automated)
- **Location**: `/home/ubuntu/backups/`
- **What**: Full application + database + uploads
- **Retention**: Manual cleanup recommended
- **Script**: `/home/ubuntu/backup-marketplace.sh`

#### Tier 3: Local Backups
- **Frequency**: On-demand
- **Location**: `C:\xampp\ocsapp\backups\`
- **What**: Complete copy for local development/disaster recovery
- **Retention**: Keep latest 2-3 backups

---

## 🚀 CREATING NEW BACKUPS

### Automated Server Backup

#### Run the Backup Script
```bash
ssh -i "C:\Users\dimit\Downloads\ocs-marketplace-key.pem" ubuntu@3.145.4.146
/home/ubuntu/backup-marketplace.sh
```

#### What Gets Backed Up:
1. ✅ Application files (app/, config/, routes/, public/)
2. ✅ Configuration files (.env, composer.json, etc.)
3. ✅ Database dump (compressed)
4. ✅ Public uploads folder
5. ✅ Storage uploads folder
6. ✅ Documentation files (*.md)

#### What's Excluded:
- ❌ vendor/ (can be restored via composer)
- ❌ storage/cache/ (temporary)
- ❌ storage/logs/ (not needed)
- ❌ .git/ (version control)

### Schedule Automated Backups (Optional)

#### Add to Crontab
```bash
# SSH to server
ssh -i "C:\Users\dimit\Downloads\ocs-marketplace-key.pem" ubuntu@3.145.4.146

# Edit crontab
crontab -e

# Add daily backup at 2 AM
0 2 * * * /home/ubuntu/backup-marketplace.sh >> /home/ubuntu/backup.log 2>&1

# Or weekly backup (Sunday at 2 AM)
0 2 * * 0 /home/ubuntu/backup-marketplace.sh >> /home/ubuntu/backup.log 2>&1
```

### Download Latest Backup to Local

```bash
# Find latest backup
ssh -i "C:\Users\dimit\Downloads\ocs-marketplace-key.pem" ubuntu@3.145.4.146 \
  "ls -lt /home/ubuntu/backups/*.tar.gz | head -1"

# Download it (replace with actual filename)
cd /c/xampp/ocsapp/backups
scp -i "/c/Users/dimit/Downloads/ocs-marketplace-key.pem" \
  ubuntu@3.145.4.146:/home/ubuntu/backups/ocs_marketplace_backup_YYYYMMDD_HHMMSS.tar.gz ./
```

---

## 📥 RECOVERY PROCEDURES

### Scenario 1: Restore Database Only

#### From Local Backup
```bash
# Extract database from backup
cd /c/xampp/ocsapp/backups
gunzip -c ocs_marketplace_backup_20251120_202250/database.sql.gz > restored_db.sql

# Or use the standalone dump
# marketplace_db_20251120.sql is already uncompressed

# Restore to server
mysql -h ocs-marketplace-db.c98w8ccoghgv.us-east-2.rds.amazonaws.com \
  -u ocs_admin -pt4Dru3gVenBIa3Jhj3ze marketplace_db < marketplace_db_20251120.sql
```

#### From Server Backup
```bash
ssh -i "C:\Users\dimit\Downloads\ocs-marketplace-key.pem" ubuntu@3.145.4.146

# Extract database
cd /home/ubuntu/backups
tar -xzf ocs_marketplace_backup_20251120_202250.tar.gz
gunzip ocs_marketplace_backup_20251120_202250/database.sql.gz

# Restore
mysql -h ocs-marketplace-db.c98w8ccoghgv.us-east-2.rds.amazonaws.com \
  -u ocs_admin -pt4Dru3gVenBIa3Jhj3ze marketplace_db \
  < ocs_marketplace_backup_20251120_202250/database.sql
```

### Scenario 2: Restore Application Files Only

#### From Server Backup
```bash
ssh -i "C:\Users\dimit\Downloads\ocs-marketplace-key.pem" ubuntu@3.145.4.146

# Extract application files
cd /home/ubuntu/backups
tar -xzf ocs_marketplace_backup_20251120_202250.tar.gz
cd ocs_marketplace_backup_20251120_202250

# Extract to temporary location first
tar -xzf app_files.tar.gz -C /home/ubuntu/temp_restore/

# Review and copy specific files as needed
cp -r /home/ubuntu/temp_restore/app/* /var/www/html/marketplace/app/
cp -r /home/ubuntu/temp_restore/config/* /var/www/html/marketplace/config/

# Set permissions
sudo chown -R www-data:www-data /var/www/html/marketplace/
sudo chmod -R 755 /var/www/html/marketplace/
```

### Scenario 3: Restore Uploads/Media Files

#### Restore Public Uploads
```bash
ssh -i "C:\Users\dimit\Downloads\ocs-marketplace-key.pem" ubuntu@3.145.4.146

cd /home/ubuntu/backups
tar -xzf ocs_marketplace_backup_20251120_202250.tar.gz
cd ocs_marketplace_backup_20251120_202250

# Extract uploads
tar -xzf uploads.tar.gz -C /var/www/html/marketplace/public/

# Set permissions
sudo chown -R www-data:www-data /var/www/html/marketplace/public/uploads/
sudo chmod -R 755 /var/www/html/marketplace/public/uploads/
```

#### Restore Storage Uploads
```bash
# Extract storage uploads
tar -xzf storage_uploads.tar.gz -C /var/www/html/marketplace/storage/

# Set permissions
sudo chown -R www-data:www-data /var/www/html/marketplace/storage/uploads/
sudo chmod -R 755 /var/www/html/marketplace/storage/uploads/
```

### Scenario 4: Complete Disaster Recovery (Full Restore)

#### Prerequisites
- Fresh server or clean installation
- Apache and PHP installed
- MySQL/RDS accessible
- Domain/DNS configured

#### Step-by-Step Recovery

```bash
# 1. SSH to server
ssh -i "C:\Users\dimit\Downloads\ocs-marketplace-key.pem" ubuntu@3.145.4.146

# 2. Upload backup to server (if not already there)
# Run this from your local machine:
scp -i "/c/Users/dimit/Downloads/ocs-marketplace-key.pem" \
  /c/xampp/ocsapp/backups/ocs_marketplace_backup_20251120_202250.tar.gz \
  ubuntu@3.145.4.146:/home/ubuntu/

# 3. Extract full backup
cd /home/ubuntu
tar -xzf ocs_marketplace_backup_20251120_202250.tar.gz
cd ocs_marketplace_backup_20251120_202250

# 4. Create application directory
sudo mkdir -p /var/www/html/marketplace
cd /var/www/html/marketplace

# 5. Extract application files
sudo tar -xzf /home/ubuntu/ocs_marketplace_backup_20251120_202250/app_files.tar.gz -C ./

# 6. Extract uploads
sudo tar -xzf /home/ubuntu/ocs_marketplace_backup_20251120_202250/uploads.tar.gz -C ./public/
sudo tar -xzf /home/ubuntu/ocs_marketplace_backup_20251120_202250/storage_uploads.tar.gz -C ./storage/

# 7. Restore database
gunzip -c /home/ubuntu/ocs_marketplace_backup_20251120_202250/database.sql.gz | \
  mysql -h ocs-marketplace-db.c98w8ccoghgv.us-east-2.rds.amazonaws.com \
  -u ocs_admin -pt4Dru3gVenBIa3Jhj3ze marketplace_db

# 8. Install Composer dependencies
cd /var/www/html/marketplace
composer install --no-dev --optimize-autoloader

# 9. Set permissions
sudo chown -R www-data:www-data /var/www/html/marketplace/
sudo chmod -R 755 /var/www/html/marketplace/
sudo chmod -R 775 /var/www/html/marketplace/storage/
sudo chmod -R 775 /var/www/html/marketplace/public/uploads/

# 10. Configure Apache (if needed)
sudo nano /etc/apache2/sites-available/marketplace.conf

# 11. Enable site and restart Apache
sudo a2ensite marketplace
sudo systemctl restart apache2

# 12. Verify .env configuration
nano /var/www/html/marketplace/.env

# 13. Clear cache
php public/clear-cache.php

# 14. Test application
curl http://localhost/marketplace
```

---

## 🔍 VERIFICATION CHECKLIST

After any restore operation, verify the following:

### Database Verification
- [ ] Tables exist: `SHOW TABLES;` should show 47 tables
- [ ] Users exist: `SELECT COUNT(*) FROM users;`
- [ ] Products exist: `SELECT COUNT(*) FROM products;`
- [ ] Orders intact: `SELECT COUNT(*) FROM orders;`
- [ ] Shops intact: `SELECT COUNT(*) FROM shops;`

### Application Verification
- [ ] Homepage loads: `https://ocsapp.ca/`
- [ ] Login works: `/login`
- [ ] Admin panel accessible: `/admin`
- [ ] Seller dashboard accessible: `/seller`
- [ ] Products display correctly
- [ ] Images load properly
- [ ] Cart functionality works
- [ ] Checkout process works

### File Verification
```bash
# Check directory structure
ls -la /var/www/html/marketplace/

# Check uploads exist
ls -la /var/www/html/marketplace/public/uploads/

# Check storage exists
ls -la /var/www/html/marketplace/storage/

# Check permissions
ls -la /var/www/html/marketplace/storage/
ls -la /var/www/html/marketplace/public/uploads/
```

### Configuration Verification
- [ ] `.env` file exists and correct
- [ ] Database connection works
- [ ] Stripe keys configured
- [ ] Mail configuration correct
- [ ] File upload directories writable

---

## 🛡️ BACKUP BEST PRACTICES

### DO:
✅ Run backups before major updates
✅ Test restore procedures regularly
✅ Keep multiple backup versions (3-5 recent)
✅ Store backups in multiple locations (server + local + S3)
✅ Encrypt backups containing sensitive data
✅ Document backup procedures
✅ Verify backup integrity after creation
✅ Automate backup process with cron
✅ Monitor backup success/failure

### DON'T:
❌ Rely on single backup location
❌ Delete old backups without verification
❌ Skip testing restore procedures
❌ Store backups in same location as production
❌ Forget to backup before deployments
❌ Ignore backup script failures
❌ Share backup files publicly (contain credentials)

---

## 📊 BACKUP RETENTION POLICY

### Recommended Retention:
- **Daily Backups**: Keep last 7 days
- **Weekly Backups**: Keep last 4 weeks
- **Monthly Backups**: Keep last 3 months
- **Major Version Backups**: Keep indefinitely

### Cleanup Old Backups
```bash
# List backups older than 30 days
find /home/ubuntu/backups/ -name "*.tar.gz" -mtime +30

# Delete backups older than 30 days (use with caution!)
find /home/ubuntu/backups/ -name "*.tar.gz" -mtime +30 -delete

# Keep only last 5 backups
cd /home/ubuntu/backups/
ls -t *.tar.gz | tail -n +6 | xargs rm -f
```

---

## 🆘 EMERGENCY CONTACTS & RESOURCES

### Key Credentials
- **Database**: ocs-marketplace-db.c98w8ccoghgv.us-east-2.rds.amazonaws.com
- **DB User**: ocs_admin
- **DB Password**: Stored in `.env` file
- **AWS Region**: us-east-2

### Important Files
- `.env` - Environment configuration
- `composer.json` - Dependency manifest
- `routes/web.php` - Application routes
- `config/database.php` - Database configuration

### Recovery Time Objectives (RTO)
- **Database Only**: ~15 minutes
- **Application Files**: ~30 minutes
- **Complete Disaster Recovery**: ~2-3 hours

### Recovery Point Objectives (RPO)
- **AWS RDS**: Up to 24 hours (daily snapshots)
- **Server Backups**: Since last backup run
- **Real-time**: Use AWS RDS point-in-time recovery

---

## 📝 BACKUP SCRIPT REFERENCE

### Backup Script Location
`/home/ubuntu/backup-marketplace.sh`

### What the Script Does:
1. Creates timestamped backup directory
2. Backs up application files (excludes vendor, cache, logs, .git)
3. Backs up public uploads folder
4. Backs up storage uploads folder
5. Dumps and compresses database
6. Creates final compressed archive
7. Cleans up temporary files
8. Reports backup size and location

### Script Output:
- Backup file: `/home/ubuntu/backups/ocs_marketplace_backup_YYYYMMDD_HHMMSS.tar.gz`
- Typical size: ~30-40 MB compressed

### Manual Backup Commands

#### Database Only
```bash
mysqldump -h ocs-marketplace-db.c98w8ccoghgv.us-east-2.rds.amazonaws.com \
  -u ocs_admin -pt4Dru3gVenBIa3Jhj3ze marketplace_db \
  | gzip > marketplace_db_$(date +%Y%m%d).sql.gz
```

#### Application Files Only
```bash
cd /var/www/html/marketplace
tar -czf ~/marketplace_app_$(date +%Y%m%d).tar.gz \
  --exclude='vendor' --exclude='storage/cache/*' --exclude='.git' \
  app/ config/ routes/ public/ .env composer.json
```

#### Uploads Only
```bash
cd /var/www/html/marketplace/public
tar -czf ~/marketplace_uploads_$(date +%Y%m%d).tar.gz uploads/
```

---

## 🔐 SECURITY CONSIDERATIONS

### Protecting Backups:
1. **Encryption**: Consider encrypting backups with GPG
   ```bash
   gpg --encrypt --recipient your@email.com backup.tar.gz
   ```

2. **Access Control**: Restrict backup directory permissions
   ```bash
   chmod 700 /home/ubuntu/backups/
   ```

3. **Credentials**: Never commit `.env` or backups to public repositories

4. **Transfer**: Use SCP/SFTP instead of FTP for transfers

5. **Storage**: Use AWS S3 with encryption enabled for long-term storage

---

## 📞 SUPPORT & TROUBLESHOOTING

### Common Issues

#### "Permission Denied" Errors
```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/html/marketplace/

# Fix permissions
sudo chmod -R 755 /var/www/html/marketplace/
sudo chmod -R 775 /var/www/html/marketplace/storage/
```

#### Database Connection Failed
- Check `.env` file has correct credentials
- Verify RDS security group allows connection
- Test connection: `mysql -h [HOST] -u [USER] -p`

#### Uploads Not Showing
- Verify uploads directory exists
- Check file permissions (755 for directories, 644 for files)
- Ensure Apache has read access

#### 500 Internal Server Error
- Check Apache error log: `/var/log/apache2/error.log`
- Enable debug mode in `.env`: `APP_DEBUG=true`
- Verify file permissions

---

## ✅ QUICK REFERENCE COMMANDS

### Backup Commands
```bash
# Run full backup
/home/ubuntu/backup-marketplace.sh

# Database backup only
mysqldump -h [HOST] -u [USER] -p[PASS] [DB] > backup.sql

# Download latest backup
scp -i "key.pem" ubuntu@3.145.4.146:/home/ubuntu/backups/*.tar.gz ./
```

### Restore Commands
```bash
# Restore database
mysql -h [HOST] -u [USER] -p[PASS] [DB] < backup.sql

# Extract full backup
tar -xzf backup.tar.gz

# Extract specific component
tar -xzf backup/app_files.tar.gz -C /destination/
```

### Verification Commands
```bash
# Check database tables
mysql -h [HOST] -u [USER] -p[PASS] -e "SHOW TABLES;" [DB]

# Check file integrity
tar -tzf backup.tar.gz | head

# Test application
curl -I https://ocsapp.ca/
```

---

**Last Updated**: 2025-11-20
**Document Version**: 1.0
**Maintained By**: Development Team

---

## 📄 Change Log

### 2025-11-20
- Initial backup and recovery documentation
- Created automated backup script
- Established three-tier backup strategy
- Documented complete disaster recovery procedures
- Added verification checklists and best practices
