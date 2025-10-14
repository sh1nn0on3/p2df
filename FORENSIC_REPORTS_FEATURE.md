# 📝 Forensic Reports Feature - Documentation

## Tổng quan

Tính năng **Forensic Reports** cho phép Investigator viết báo cáo điều tra chuyên nghiệp về các email đã giải mã, kèm theo trích xuất logs audit trail liên quan.

---

## ✨ Tính năng mới

### 1. **Forensic Report Creation**
- Investigator có thể tạo báo cáo điều tra sau khi giải mã email
- Báo cáo bao gồm:
  - **Title**: Tiêu đề báo cáo
  - **Severity**: Mức độ nghiêm trọng (Low, Medium, High, Critical)
  - **Findings**: Phát hiện chính
  - **Analysis**: Phân tích chi tiết
  - **Recommendations**: Khuyến nghị hành động
  - **Related Logs**: Logs audit trail liên quan

### 2. **Audit Trail Extraction**
- Tự động trích xuất logs liên quan đến email đang điều tra
- Cho phép chọn logs nào sẽ đính kèm vào báo cáo
- Timeline visualization cho logs
- Hiển thị đầy đủ: user, action, timestamp, IP address

### 3. **Professional Logs Viewer**
- Giao diện logs được cải thiện:
  - Timeline view với visual indicators
  - Color coding theo role và action
  - Filter nhanh theo action type
  - Statistics overview
  - Detailed information in modals
  - Responsive và modern design

---

## 🗄️ Database Schema

### Bảng `forensic_reports`

```sql
CREATE TABLE forensic_reports (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    email_id BIGINT UNSIGNED NOT NULL,
    investigator_id BIGINT UNSIGNED NOT NULL,
    decryption_request_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    findings LONGTEXT NOT NULL,
    analysis LONGTEXT NOT NULL,
    recommendations LONGTEXT NULL,
    related_logs JSON NULL,
    status ENUM('draft', 'completed', 'reviewed') DEFAULT 'draft',
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (email_id, investigator_id, status),
    FOREIGN KEY (email_id) REFERENCES emails(id) ON DELETE CASCADE,
    FOREIGN KEY (investigator_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (decryption_request_id) REFERENCES decryption_requests(id) ON DELETE SET NULL
);
```

---

## 🚀 Workflow

### Bước 1: Decrypt Email
1. Investigator request decryption
2. Admin approve
3. Investigator decrypt và xem nội dung

### Bước 2: Create Report
1. Từ trang decrypted email, click **"Create Forensic Report"**
2. Hệ thống tự động load related logs
3. Fill in report details:
   - Title (required)
   - Severity level (required)
   - Key findings (min 50 chars)
   - Detailed analysis (min 100 chars)
   - Recommendations (optional)
4. Select related logs từ sidebar
5. Save as **Draft** hoặc mark as **Completed**

### Bước 3: View & Edit Report
1. Navigate to **"Reports"** menu
2. View list of your reports
3. Click **"View"** để xem report chi tiết với logs timeline
4. Click **"Edit"** nếu report còn là draft
5. Update và mark as completed khi hoàn thành

---

## 📋 Routes mới

### Investigator Routes

```php
// Reports Management
GET  /investigator/reports                     → List all reports
GET  /investigator/emails/{id}/create-report   → Create report form
POST /investigator/emails/{id}/create-report   → Store new report
GET  /investigator/reports/{id}                → View report detail
GET  /investigator/reports/{id}/edit           → Edit report form
PUT  /investigator/reports/{id}                → Update report

// AJAX
GET  /investigator/emails/{id}/logs            → Extract related logs (JSON)
```

---

## 🎨 UI Improvements

### Logs Viewer (Investigator)
- **Timeline View**: Visual timeline với dots và connecting lines
- **Color Coding**: Mỗi action có màu riêng
- **Quick Filters**: Sidebar filters theo action type
- **Statistics**: Tổng quan về số lượng logs
- **Hover Effects**: Smooth transitions và tooltips

### Logs Viewer (Admin)
- **Table View**: Professional table layout
- **Role Highlighting**: Admin vs Investigator logs
- **Statistics Banner**: Overview card với gradients
- **Details Modal**: Click vào details button để xem JSON
- **Advanced Filters**: Filter theo role + action
- **Responsive Design**: Mobile-friendly

### Report Views
- **Severity Color Coding**: Border colors theo severity
- **Timeline for Logs**: Visual audit trail
- **Collapsible Details**: Expandable log details
- **Print-Friendly**: Print button cho report export

---

## 🔍 Screenshots Locations

### 1. Create Report Page
- Split layout: Form bên trái, Logs bên phải
- Checkbox selection cho logs
- Select All / Deselect All buttons
- Real-time visual feedback khi select logs

### 2. View Report Page
- Professional report layout
- Severity badge prominent
- Timeline visualization cho logs
- Export options (Print, PDF)

### 3. Reports List Page
- Table view với severity badges
- Status indicators (Draft/Completed/Reviewed)
- Quick actions (View, Edit)
- Filter by status

### 4. Improved Logs Pages
- Timeline design cho investigator
- Table design cho admin
- Statistics cards
- Filter sidebars
- Modal details

---

## 💡 Usage Examples

### Example 1: Phishing Investigation

```
Title: Suspicious Phishing Email - CEO Impersonation Attempt

Severity: High

Findings:
- Email sender address spoofed to mimic CEO email
- Contains link to fake login page
- Urgency language attempting to bypass security protocols
- Received by 15 employees in finance department

Analysis:
The email exhibits classic phishing characteristics:
1. Sender address john.ceo@company-inc.com (fake) vs john.ceo@company.com (real)
2. Link points to phishing site at company-login-secure.xyz
3. Email body uses urgent tone: "Immediate action required"
4. No digital signature present
5. Sent outside business hours (2:30 AM)

Analysis of related logs shows:
- Email was received at 02:32:15
- First viewed by security team at 08:15:43
- Decryption request submitted at 08:20:12
- Approved by admin at 08:35:50
- Full analysis completed at 09:10:23

Recommendations:
1. Block sender domain company-inc.com immediately
2. Send warning to all employees about this phishing attempt
3. Implement DMARC policy to prevent email spoofing
4. Conduct security awareness training
5. Monitor for similar patterns in future emails
6. Report to relevant authorities

Related Logs: [5 logs selected]
- view_email (Security Analyst, 08:15:43)
- request_decrypt (Security Analyst, 08:20:12)
- approve_request (Admin, 08:35:50)
- decrypt_email (Security Analyst, 08:40:33)
- create_report (Security Analyst, 09:10:23)
```

---

## 🛠️ Testing Checklist

### Functionality Tests

- [ ] Can create report after decrypting email
- [ ] Related logs auto-loaded correctly
- [ ] Can select/deselect individual logs
- [ ] Select All / Deselect All buttons work
- [ ] Can save as draft
- [ ] Can save as completed
- [ ] Draft reports can be edited
- [ ] Completed reports cannot be edited
- [ ] Reports list shows correct data
- [ ] View report shows selected logs in timeline
- [ ] Filter logs in sidebar works
- [ ] Statistics calculate correctly
- [ ] Severity badges show correct colors
- [ ] Print button works
- [ ] Mobile responsive design works

### Security Tests

- [ ] Cannot create report without approved request
- [ ] Cannot view other investigator's reports
- [ ] Cannot edit other investigator's reports
- [ ] Cannot access reports for unapproved emails
- [ ] Logs extraction respects permissions
- [ ] SQL injection prevented (title, findings, analysis)
- [ ] XSS prevented in report display

---

## 📊 Performance Considerations

- **Logs Loading**: Limit to last 100 related logs
- **Report Pagination**: 20 reports per page
- **AJAX Logs**: Cache results for 5 minutes
- **Timeline Rendering**: Use CSS transforms for smooth animations
- **Mobile**: Lazy load logs timeline

---

## 🔜 Future Enhancements

1. **PDF Export**: Generate PDF from report
2. **Report Templates**: Pre-defined templates for common scenarios
3. **Collaborative Reports**: Multiple investigators can contribute
4. **Report Approval**: Admin can review and approve reports
5. **Email Notifications**: Notify when report is reviewed
6. **Advanced Analytics**: ML-powered insights from reports
7. **Report Sharing**: Share reports with external parties (encrypted)
8. **Timeline Visualization**: Interactive timeline for investigation
9. **Evidence Attachment**: Attach screenshots or files to reports
10. **Report Versioning**: Track changes to reports over time

---

## 🎓 For Thesis Demonstration

### Demo Script (5 phút)

1. **Show Decrypted Email** (1 min)
   - Show plaintext content
   - Point out "Create Forensic Report" button

2. **Create Report** (2 min)
   - Fill in title, severity
   - Explain findings và analysis
   - Show related logs sidebar
   - Select relevant logs
   - Save as completed

3. **View Report** (1 min)
   - Show professional layout
   - Highlight severity indication
   - Show timeline of selected logs
   - Demonstrate print functionality

4. **Show Improved Logs** (1 min)
   - Navigate to Logs page
   - Show timeline view
   - Use quick filters
   - Show statistics

---

## 🎉 Benefits

1. **Professional Documentation**: Standardized forensic reports
2. **Audit Trail**: Complete evidence chain with logs
3. **Transparency**: All investigation steps documented
4. **Collaboration**: Reports can be shared with team
5. **Legal Compliance**: Proper documentation for legal proceedings
6. **Knowledge Base**: Reports serve as reference for future investigations
7. **Performance Metrics**: Track investigation patterns and timelines

---

**✅ Feature Complete and Ready for Use!**

Test at: `http://localhost:8000`
Login as Investigator → Decrypt Email → Create Report

