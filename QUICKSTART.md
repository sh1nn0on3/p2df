# 🚀 Quick Start Guide - Test P2DF System

## ✅ System Status

Server đang chạy tại: **http://localhost:8000**

Database đã được seed với:
- ✅ 3 users (1 admin + 2 investigators)
- ✅ RSA keys cho mỗi user
- ✅ 5 sample encrypted emails

---

## 🧪 Test Flow (5 phút)

### Test 1: Login as Admin (1 min)

1. Mở browser: `http://localhost:8000`
2. Login:
   - Email: `admin@example.com`
   - Password: `password`
3. ✅ Kiểm tra Dashboard:
   - Total Emails: 5
   - Pending Requests: 0
   - Approved Requests: 0
   - Rejected Requests: 0

### Test 2: View Encrypted Emails (1 min)

1. Click menu **"Emails"**
2. ✅ Xem 5 emails đã mã hóa
3. ✅ Thấy encrypted body preview (gibberish text)

### Test 3: Login as Investigator (1 min)

1. Click **Logout** (dropdown góc phải)
2. Login lại:
   - Email: `inv1@example.com`
   - Password: `password`
3. ✅ Dashboard hiển thị:
   - Available Emails: 5
   - My Pending Requests: 0

### Test 4: Request Decryption (1 min)

1. Click menu **"Emails"**
2. Chọn email đầu tiên → Click **"View"**
3. ✅ Thấy metadata (from, to, subject)
4. ✅ Thấy encrypted content (không đọc được)
5. Scroll xuống form **"Request Decryption"**
6. Nhập reason:
   ```
   Investigating potential security breach in quarterly report
   ```
7. Click **"Submit Request"**
8. ✅ Thấy alert: "Decryption request submitted successfully"
9. ✅ Status badge: "Pending"

### Test 5: Admin Approve Request (1 min)

1. Logout → Login as Admin (`admin@example.com` / `password`)
2. Click menu **"Requests"**
3. ✅ Thấy 1 request pending từ Investigator One
4. Click **"Approve"** button (màu xanh)
5. Confirm approve
6. ✅ Status chuyển sang "Approved"

### Test 6: Investigator Decrypt Email (1 min)

1. Logout → Login as Investigator (`inv1@example.com` / `password`)
2. Click menu **"My Requests"**
3. ✅ Thấy request status = "Approved"
4. Click **"Decrypt"** button (màu xanh)
5. ✅ Xem plaintext email content:
   ```
   Hi Jane,

   Please review the attached quarterly report. 
   We need to discuss the Q3 results in tomorrow's meeting.

   Best regards,
   John
   ```
6. ✅ Thấy badge "Hash Verified - Content Integrity OK" (màu xanh)

### Test 7: View Forensic Logs (30 sec)

1. Login as Admin
2. Click menu **"Logs"**
3. ✅ Xem tất cả actions đã được log:
   - `login` (admin, investigator)
   - `view_email` (investigator)
   - `request_decrypt` (investigator)
   - `approve_request` (admin)
   - `decrypt_email` (investigator)

---

## 🎯 Advanced Testing

### Test Upload New Emails

1. Login as Admin
2. Click **"Upload"**
3. Upload file: `sample_emails.csv`
4. ✅ Xem emails mới được mã hóa và thêm vào database

### Test Reject Request

1. Investigator request decryption
2. Admin click **"Reject"**
3. ✅ Status = "Rejected"
4. ✅ Investigator không thể decrypt

### Test Multiple Investigators

1. Login as `inv2@example.com` / `password`
2. Request decryption cho email khác
3. Admin approve
4. Investigator 2 decrypt thành công
5. ✅ Mỗi investigator có riêng RSA keys

---

## 📊 Check Database

Trong MySQL command line hoặc phpMyAdmin:

```sql
-- Xem users và keys
SELECT id, name, email, role, 
       public_key_path, private_key_path 
FROM users;

-- Xem encrypted emails
SELECT id, `from`, `to`, subject, 
       LEFT(body_encrypted, 50) as encrypted_preview,
       LEFT(aes_key_encrypted_admin, 50) as key_preview
FROM emails;

-- Xem decryption requests
SELECT dr.id, 
       e.subject, 
       u.name as investigator, 
       dr.status,
       dr.created_at,
       dr.approved_at
FROM decryption_requests dr
JOIN emails e ON dr.email_id = e.id
JOIN users u ON dr.investigator_id = u.id
ORDER BY dr.id DESC;

-- Xem forensic logs
SELECT fl.id, 
       u.name as user, 
       fl.role, 
       fl.action, 
       fl.target_id,
       fl.ip_address,
       fl.created_at
FROM forensic_logs fl
JOIN users u ON fl.user_id = u.id
ORDER BY fl.created_at DESC
LIMIT 20;
```

---

## 🔍 Verify Encryption

### Check RSA Keys

```bash
# Windows
dir storage\keys\

# Expected files:
# - admin_public.pem
# - admin_private.pem
# - investigator1_public.pem
# - investigator1_private.pem
# - investigator2_public.pem
# - investigator2_private.pem
# - openssl_temp.cnf
```

### Verify Keys are Valid

```bash
# Test admin public key (in project root)
openssl rsa -pubin -in storage/keys/admin_public.pem -text -noout
```

---

## 🛠️ Troubleshooting

### Server not responding?

Check if server is running:
```bash
# Nếu server dừng, chạy lại:
php artisan serve
```

### Cannot login?

Verify database has users:
```sql
SELECT email, role FROM users;
```

If empty, re-seed:
```bash
php artisan db:seed
```

### Decrypt fails?

1. Check request status = "approved"
2. Check AES key exists in `decryption_requests.aes_key_encrypted_inv`
3. Check RSA keys exist in `storage/keys/`

---

## 📸 Expected Screenshots

### 1. Admin Dashboard
- 4 colored cards with statistics
- Quick Actions menu
- System information

### 2. Admin Requests Page
- Table với pending requests
- Approve/Reject buttons
- Investigator name, email subject, reason

### 3. Investigator Email View
- Metadata only (from, to, subject)
- Encrypted content (không đọc được)
- Request form with reason textarea

### 4. Decrypted Email Page
- Full plaintext content
- Green "Hash Verified" badge
- From/To/Subject/Date info

### 5. Forensic Logs
- Table of all actions
- User, role, action, target_id, IP, timestamp
- Filter by role/action

---

## ✅ Success Criteria

Hệ thống hoạt động đúng nếu:

- [x] Admin có thể upload và mã hóa emails
- [x] Investigator chỉ xem được metadata
- [x] Investigator phải request để decrypt
- [x] Admin control việc approve/reject
- [x] Chỉ approved requests mới decrypt được
- [x] Decrypted content khớp với original
- [x] Hash verification pass (green badge)
- [x] Mọi actions đều được log
- [x] IP address được track
- [x] Không thể bypass approval workflow

---

## 🎓 For Thesis Demonstration

### Demo Script (10 phút)

1. **Giới thiệu hệ thống** (1 min)
   - P2DF model
   - Privacy-preserving
   - Role-based access

2. **Admin upload email** (2 min)
   - Show CSV format
   - Upload và encryption process
   - Show encrypted data in database

3. **Investigator workflow** (3 min)
   - Browse metadata
   - Submit request với justification
   - Show pending status

4. **Admin review** (2 min)
   - Show request details
   - Explain AES key re-encryption
   - Approve request

5. **Decryption** (2 min)
   - Investigator decrypt
   - Show plaintext
   - Verify hash integrity

6. **Audit trail** (1 min)
   - Show forensic logs
   - Every action tracked
   - Transparency and accountability

---

## 📝 Demo Accounts

Keep these credentials handy:

```
Admin:
  Email: admin@example.com
  Password: password
  
Investigator 1:
  Email: inv1@example.com
  Password: password
  
Investigator 2:
  Email: inv2@example.com
  Password: password
```

---

**🎉 Hệ thống sẵn sàng cho demo và testing!**

Mở browser tại: **http://localhost:8000**

