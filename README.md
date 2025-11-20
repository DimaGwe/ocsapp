# OCS Marketplace - Local Development Repository

This repository contains documentation, context files, and backup resources for the OCS Marketplace application.

## 📋 Repository Contents

### Documentation
- **[OCS_MARKETPLACE_CONTEXT.md](OCS_MARKETPLACE_CONTEXT.md)** - Complete application context and architecture documentation
- **[BACKUP_RECOVERY_GUIDE.md](BACKUP_RECOVERY_GUIDE.md)** - Comprehensive backup and disaster recovery procedures

### Local Resources
- **backups/** - Full application backups (not tracked in git)
  - Complete compressed backup (32 MB)
  - Database dump (1.3 MB SQL)
  - Extracted backup components
- **marketplace.env** - Production environment configuration (not tracked in git)

## 🚀 Production Server

- **URL**: https://ocsapp.ca
- **Server**: AWS EC2 (3.145.4.146)
- **Database**: AWS RDS MySQL 8.0
- **Application Path**: `/var/www/html/marketplace/`

## 📖 Quick Start

### Accessing Documentation
1. **Application Overview**: See [OCS_MARKETPLACE_CONTEXT.md](OCS_MARKETPLACE_CONTEXT.md)
2. **Backup Procedures**: See [BACKUP_RECOVERY_GUIDE.md](BACKUP_RECOVERY_GUIDE.md)

### SSH to Production Server
```bash
ssh -i "C:\Users\dimit\Downloads\ocs-marketplace-key.pem" ubuntu@3.145.4.146
```

### Create New Backup
```bash
ssh -i "C:\Users\dimit\Downloads\ocs-marketplace-key.pem" ubuntu@3.145.4.146
/home/ubuntu/backup-marketplace.sh
```

### Download Latest Backup
```bash
cd /c/xampp/ocsapp/backups
scp -i "/c/Users/dimit/Downloads/ocs-marketplace-key.pem" \
  ubuntu@3.145.4.146:/home/ubuntu/backups/ocs_marketplace_backup_*.tar.gz ./
```

## 🏗️ Application Architecture

- **Framework**: Custom PHP MVC
- **PHP Version**: 8.2.29
- **Database**: MySQL 8.0 (47 tables)
- **Payment**: Stripe Checkout
- **Languages**: English/French (Bilingual)
- **Controllers**: 27+
- **Views**: 77+

## 📦 Key Features

- Multi-vendor marketplace
- Inventory management (Model B with auto-allocation)
- Order processing & tracking
- Delivery management system
- Analytics & reporting
- SEO optimization
- Visitor tracking
- Email notifications
- Bilingual support (EN/FR)

## 🔐 Security Notes

⚠️ **IMPORTANT**: This repository contains local backups and environment files with sensitive information:
- Database credentials
- Stripe API keys
- AWS access keys
- Production .env file

**These files are excluded from git** via `.gitignore` and should NEVER be committed to version control.

## 📚 Additional Resources

### Server-Side Documentation
The production server contains additional documentation in `/var/www/html/marketplace/`:
- ADMIN-STOCK-UI-UPDATE.md
- AWS-DEPLOYMENT-GUIDE.md
- BILINGUAL-UPDATE.md
- CLAUDE.md
- PAYMENT-SETUP.md
- SEO_IMPLEMENTATION_COMPLETE.md
- SESSION-NOTES.md
- WORKFLOW-AUDIT.md
- And more...

### Production Files Location
```
/var/www/html/marketplace/
├── app/                    # Application code
├── config/                 # Configuration files
├── database/               # Migrations
├── public/                 # Web root
├── routes/                 # Route definitions
├── storage/                # Uploads, cache, logs
└── vendor/                 # Composer dependencies
```

## 🛠️ Development Workflow

1. **Before Making Changes**: Create a backup
   ```bash
   /home/ubuntu/backup-marketplace.sh
   ```

2. **Make Changes**: Edit files on server or develop locally

3. **Test Changes**: Verify functionality in production (or staging)

4. **Commit to Git**: Use the marketplace git repository
   ```bash
   cd /var/www/html/marketplace
   git add .
   git commit -m "Description of changes"
   git push origin main
   ```

5. **Document Changes**: Update relevant .md files

## 📊 Repository Structure

```
ocsapp/
├── .claude/                         # Claude Code settings
├── backups/                         # Backup files (git ignored)
│   ├── ocs_marketplace_backup_*.tar.gz
│   ├── marketplace_db_*.sql
│   └── [extracted files]
├── .gitignore                       # Git ignore rules
├── BACKUP_RECOVERY_GUIDE.md        # Backup documentation
├── OCS_MARKETPLACE_CONTEXT.md      # Application context
├── README.md                        # This file
└── marketplace.env                  # Production config (git ignored)
```

## 🔄 Backup Strategy

### Three-Tier System
1. **AWS RDS**: Automated daily database snapshots (7-day retention)
2. **Server Backups**: On-demand full backups at `/home/ubuntu/backups/`
3. **Local Backups**: Downloaded copies in `backups/` directory

### Backup Contents
- Application code (15 MB)
- Database dump (178 KB compressed)
- Public uploads (7.6 MB)
- Storage uploads (9.3 MB)
- Configuration files

## 📞 Support

For issues or questions:
1. Check documentation in this repository
2. Review server logs: `/storage/logs/` or `/var/log/apache2/error.log`
3. Enable debug mode: Set `APP_DEBUG=true` in `.env`

## ⚡ Quick Commands Reference

### Database Access
```bash
mysql -h ocs-marketplace-db.c98w8ccoghgv.us-east-2.rds.amazonaws.com \
  -u ocs_admin -p marketplace_db
```

### Application Directory
```bash
cd /var/www/html/marketplace
```

### Check Application Status
```bash
sudo systemctl status apache2
git status
php -v
```

### Clear Cache
```bash
php public/clear-cache.php
```

---

**Last Updated**: 2025-11-20
**Repository Owner**: DimaGwe
**Production URL**: https://ocsapp.ca
