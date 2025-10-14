# 🌟 P2DF Email Forensic System - Features

## 🔐 Core P2DF Implementation

### Privacy-Preserving Architecture
- **Dual-Layer Encryption**: AES-256 for content + RSA-2048 for key management
- **Role-Based Key Management**: Each user has dedicated RSA key pair
- **Zero-Knowledge Design**: Admin never sees plaintext, Investigator needs approval

### Encryption Flow
```
1. Admin uploads email → Generate unique AES-256 key
2. Encrypt email body with AES key
3. Encrypt AES key with Admin's RSA public key
4. Store encrypted data in database

When approved:
5. Admin decrypts AES key using private key
6. Re-encrypts AES key with Investigator's public key
7. Investigator decrypts using own private key
8. Investigator decrypts email content with AES key
```

## 👨‍💼 Admin Features

### 1. Dashboard
- **Statistics Overview**
  - Total encrypted emails
  - Pending decryption requests
  - Approved/rejected request counts
- **Quick Actions Menu**
  - Upload new datasets
  - View all emails
  - Review requests
  - Access forensic logs

### 2. Email Upload & Encryption
- **CSV Dataset Import**
  - Support format: `from,to,subject,body`
  - Max file size: 10MB
  - Batch processing with error handling
- **Automatic Encryption**
  - Unique AES-256 key per email
  - RSA-2048 encryption of AES keys
  - SHA-256 hash for integrity verification
- **Upload Logging**
  - Every upload action tracked
  - Details: filename, count, timestamp

### 3. Decryption Request Management
- **Request Review Interface**
  - View all requests (pending/approved/rejected)
  - Filter by status
  - See investigator details and reason
- **Approval Process**
  - One-click approve/reject
  - Automatic AES key re-encryption
  - Notification of action in logs
- **Access Control**
  - Only Admin can approve requests
  - Cannot approve own requests (if Admin is also Investigator)

### 4. Email List Viewing
- **Encrypted Email Browser**
  - View all uploaded emails
  - Search by from/to/subject
  - Pagination support (20 per page)
- **Metadata Display**
  - Email ID, sender, recipient
  - Subject line
  - Encrypted content preview
  - Upload timestamp

### 5. Forensic Audit Logs
- **Comprehensive Logging**
  - All user actions tracked
  - IP address recording
  - Timestamp precision to second
- **Log Filtering**
  - Filter by role (admin/investigator)
  - Filter by action type
  - Search functionality
- **Logged Actions**
  - `login`, `logout`
  - `upload_email`
  - `request_decrypt`
  - `approve_request`, `reject_request`
  - `decrypt_email`
  - `view_email`, `view_logs`

## 🔍 Investigator Features

### 1. Dashboard
- **Personal Statistics**
  - Total available emails
  - My pending requests
  - My approved requests
  - My rejected requests
- **Recent Emails Widget**
  - Latest 10 encrypted emails
  - Quick view access

### 2. Email Discovery
- **Metadata-Only Browsing**
  - View sender, recipient, subject
  - Date/time information
  - No access to encrypted content
- **Search Functionality**
  - Search by sender email
  - Search by recipient email
  - Search by subject keywords
- **Privacy Protection**
  - Cannot view plaintext without approval
  - Encrypted body visible but unreadable

### 3. Decryption Request Workflow
- **Request Submission**
  - Select target email
  - Provide justification (min 10 chars)
  - Submit for admin review
- **Request Status Tracking**
  - View all my requests
  - Filter by status (pending/approved/rejected)
  - See submission and approval timestamps
- **Request Validation**
  - Cannot submit duplicate requests
  - Must provide valid reason

### 4. Email Decryption
- **Secure Decryption Process**
  - Available only for approved requests
  - Uses investigator's private RSA key
  - Decrypts AES key first, then email content
- **Content Viewing**
  - Display full plaintext email
  - Show metadata (from, to, subject, date)
  - Integrity verification badge
- **Hash Verification**
  - SHA-256 hash check
  - Green badge if content is untampered
  - Red warning if hash mismatch

### 5. Activity History
- **Personal Audit Trail**
  - View own forensic logs
  - See all actions performed
  - IP address tracking
- **Transparency**
  - Cannot delete or modify logs
  - Read-only access

## 🔒 Security Features

### 1. Authentication & Authorization
- **Role-Based Access Control**
  - Admin role: Full system access
  - Investigator role: Limited to approved access
- **Session Management**
  - 120-minute session timeout
  - Secure session cookies
  - CSRF protection on all forms

### 2. Cryptographic Security
- **AES-256-CBC**
  - Industry-standard symmetric encryption
  - Random IV per encryption
  - Key size: 32 bytes (256 bits)
- **RSA-2048**
  - Asymmetric key encryption
  - PKCS1 OAEP padding
  - Key storage with 0600 permissions
- **SHA-256 Hashing**
  - Content integrity verification
  - Tamper detection

### 3. Privacy Protection
- **Data Minimization**
  - Only encrypted data stored
  - Plaintext never persists
  - Private keys never transmitted
- **Access Logging**
  - Every data access logged
  - IP address tracking
  - User identification
- **Secure Key Storage**
  - Keys stored in protected directory
  - File permissions: 0600 for private keys
  - No key exposure in responses

### 4. Request Approval System
- **Multi-Step Access Control**
  - Investigator cannot self-approve
  - Admin review required
  - Reason documentation mandatory
- **Selective Decryption**
  - Per-email approval
  - No bulk access
  - Time-stamped approvals

## 📊 Forensic Logging System

### Log Structure
```json
{
  "id": 1,
  "user_id": 2,
  "role": "investigator",
  "action": "request_decrypt",
  "target_id": "5",
  "ip_address": "192.168.1.100",
  "details": {
    "email_id": 5,
    "reason": "Suspicious transaction investigation"
  },
  "created_at": "2025-10-12 23:45:12"
}
```

### Logged Events
- **Authentication**: Login/logout with IP
- **Email Operations**: Upload, view metadata
- **Access Requests**: Submit, approve, reject
- **Decryption**: Actual decryption events
- **Administrative**: Log viewing, key generation

### Audit Capabilities
- **Tamper-Proof**: Logs cannot be edited or deleted
- **Comprehensive**: All actions tracked
- **Searchable**: Filter by user, role, action
- **Exportable**: Can be extracted for analysis

## 🎨 User Interface

### Design Principles
- **Bootstrap 4**: Modern, responsive design
- **Role-Specific Navigation**: Different menus for Admin/Investigator
- **Color Coding**: Status badges (warning/success/danger)
- **Icon Usage**: Font Awesome icons for better UX

### Key UI Components
- **Dashboard Cards**: Statistics with visual hierarchy
- **Data Tables**: Sortable, paginated lists
- **Forms**: Validated with error messages
- **Alerts**: Success/error notifications
- **Status Badges**: Clear visual status indicators

### Accessibility
- **Responsive Design**: Works on desktop/tablet/mobile
- **Clear Labels**: Descriptive form labels
- **Error Handling**: User-friendly error messages
- **Visual Feedback**: Loading states, success confirmations

## 🔄 Workflow Automation

### Automatic Processes
- **RSA Key Generation**: On user creation
- **AES Key Generation**: Per email upload
- **Hash Calculation**: Automatic on encryption
- **Log Recording**: Automatic on every action

### Manual Processes (By Design)
- **Request Approval**: Requires admin decision
- **Email Decryption**: Requires approved request
- **User Creation**: Admin-controlled

## 📈 Scalability Features

### Performance Optimization
- **Pagination**: 20 items per page
- **Indexed Searches**: Database indexes on key fields
- **Lazy Loading**: Only load data when needed
- **Efficient Queries**: Eager loading relationships

### Extensibility
- **Modular Services**: CryptoService, LogService
- **Clean Architecture**: Controllers → Services → Models
- **Middleware Support**: Easy to add new middleware
- **API Ready**: Controllers can be adapted for API

## 🧪 Testing Capabilities

### Manual Testing Support
- **Seeded Data**: Pre-populated test accounts
- **Sample Emails**: 5 encrypted emails included
- **Clear Workflows**: Step-by-step testing possible
- **Log Verification**: Can verify all actions logged

### Quality Assurance
- **Error Handling**: Try-catch blocks throughout
- **Input Validation**: Laravel validation rules
- **Hash Verification**: Integrity checking
- **Permission Checks**: Role middleware

## 📚 Documentation

### Included Documentation
- **README.md**: Project overview and features
- **SETUP.md**: Step-by-step installation guide
- **FEATURES.md**: This comprehensive feature list
- **Code Comments**: Inline documentation in all classes

### Code Quality
- **PSR Standards**: Follows Laravel conventions
- **Descriptive Names**: Clear variable/method naming
- **Type Hints**: PHP 8.1 type declarations
- **Comments**: Purpose explained for complex logic

---

## 🎯 P2DF Compliance Checklist

✅ **Privacy Preservation**
- Content encrypted at rest
- Keys separated by role
- No plaintext storage

✅ **Access Control**
- Role-based permissions
- Request-approval workflow
- Cannot bypass approval

✅ **Auditability**
- Comprehensive logging
- Tamper-proof logs
- IP tracking

✅ **Transparency**
- Clear approval process
- Visible status updates
- Logged every action

✅ **Integrity Verification**
- SHA-256 hashing
- Hash verification on decrypt
- Tamper detection

✅ **Key Management**
- RSA key pairs per user
- Secure key storage
- Key re-encryption for access

---

**This system demonstrates a full P2DF implementation suitable for academic research and forensic investigation workflows.**

