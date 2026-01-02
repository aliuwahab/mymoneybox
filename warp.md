# MyPiggyBox - User Flow Documentation for Regulatory Review

**Application Name:** MyPiggyBox  
**Version:** 1.0.0  
**Document Date:** December 21, 2024  
**Purpose:** Regulatory Review and Compliance Assessment

---

## 1. Executive Summary

MyPiggyBox is a web-based contribution collection platform that enables users to create, manage, and share digital "piggy boxes" for collecting monetary contributions from others. The platform facilitates secure online payments through integrated payment providers (TrendiPay) and provides transparency through real-time tracking and reporting.

**Key Financial Model:**
- MyPiggyBox does NOT hold user funds
- All funds are held by TrendiPay (licensed payment provider)
- Users can withdraw available funds minus platform charges
- Withdrawals require identity verification with Ghana-approved national ID
- Funds transferred directly to user's bank account via TrendiPay Transfer API

### Key Features
- User account management with email verification
- Piggy box creation with customizable contribution rules
- Public and private piggy box visibility options
- Secure payment processing via TrendiPay payment gateway
- Identity verification for withdrawals (Ghana Card/National ID)
- Flexible withdrawal system with real-time fund availability
- QR code generation for easy sharing
- Real-time contribution tracking and analytics
- Multi-currency support based on user's country

---

## 2. User Registration & Authentication Flow

### 2.1 New User Registration
**Flow:** Guest → Registration Form → Email Verification → Active User

**Step-by-step Process:**
1. User visits the homepage (`/`)
2. Clicks "Register" or "Get Started"
3. Provides required information:
   - Full name
   - Email address
   - Password (minimum 8 characters)
   - Country selection (determines currency)
4. Agrees to Terms of Service and Privacy Policy
5. Submits registration form
6. System creates user account with:
   - Hashed password (bcrypt)
   - Assigned country and default currency
   - Unverified status
   - KYC status: 'unverified' (required for withdrawals)
7. Verification email sent to user's email address
8. User clicks verification link in email
9. Account status changes to "verified"
10. User redirected to dashboard

**Data Collected:**
- Name
- Email address
- Country
- IP address (for security)
- Timestamp of registration

**Security Measures:**
- Password hashing (bcrypt)
- Email verification required
- CSRF protection on all forms
- Rate limiting on registration attempts
- Captcha (if implemented)

### 2.2 User Login
**Flow:** Guest → Login Form → Dashboard

**Step-by-step Process:**
1. User visits `/login`
2. Enters email and password
3. Optional: Checks "Remember Me"
4. System validates credentials
5. If two-factor authentication enabled:
   - User receives 2FA code
   - Enters code to complete login
6. User redirected to dashboard
7. Session created with secure cookie

**Security Measures:**
- Rate limiting (prevents brute force attacks)
- Account lockout after failed attempts
- Two-factor authentication (optional)
- Session timeout after inactivity
- Secure session cookies (HTTP-only, Secure flag)

### 2.3 Password Reset
**Flow:** Forgot Password → Email → Reset Form → Login

**Step-by-step Process:**
1. User clicks "Forgot Password" on login page
2. Enters email address
3. System sends password reset link (valid for 60 minutes)
4. User clicks link in email
5. Provides new password (twice for confirmation)
6. System updates password
7. User redirected to login page
8. Previous sessions invalidated

---

## 3. Money Box (Piggy Box) Creation Flow

### 3.1 Creating a New Money Box
**Flow:** Dashboard → Create Money Box → Configuration → Save → Active Money Box

**Step-by-step Process:**

1. **Initiation**
   - User navigates to `/money-boxes/create` from dashboard
   - Access verified (must be authenticated)

2. **Basic Information**
   - Title (required, max 255 characters)
   - Description (optional, text area)
   - Category selection (e.g., Birthday, Wedding, Medical, Education)

3. **Visibility Settings**
   - **Public:** Listed on browse page, searchable by anyone
   - **Private (Unlisted):** Accessible only via direct link

4. **Contributor Identity Rules**
   - **Anonymous Allowed:** Contributors can choose to remain anonymous
   - **Must Identify:** Contributors must provide name and contact info
   - **User's Choice:** Contributors decide per contribution

5. **Contribution Amount Rules**
   - **Fixed Amount:** Specific amount required (e.g., $50.00)
   - **Variable:** Any amount accepted (minimum $0.01)
   - **Minimum Only:** Must contribute at least X amount
   - **Maximum Only:** Cannot exceed X amount
   - **Range:** Between minimum and maximum (e.g., $10 - $100)

6. **Time Settings**
   - Start date (optional - when contributions can begin)
   - End date (optional - when contributions close)
   - Ongoing checkbox (no end date)

7. **Financial Settings**
   - Goal amount (optional - target to raise)
   - Currency (auto-populated from user's country)

8. **Media Upload** (optional)
   - Main image (featured image)
   - Gallery images (multiple)
   - File size limits enforced
   - Allowed formats: JPG, PNG, GIF

9. **System Processing**
   - Generates unique slug (URL-friendly identifier)
   - Creates money box record in database
   - Status set to "active"
   - QR code generated on-demand (not immediately)
   - Initializes counters (total_contributions: 0, contribution_count: 0)
   - Initializes financial tracking (available_balance: 0, withdrawn_amount: 0)

10. **Confirmation**
    - Success message displayed
    - User redirected to money box details page
    - Share options presented

**Data Stored:**
```
- user_id (owner)
- title, slug, description
- category_id
- visibility (public/private)
- contributor_identity rule
- amount_type rule
- fixed_amount, minimum_amount, maximum_amount
- goal_amount
- currency_code
- start_date, end_date, is_ongoing
- is_active (boolean)
- qr_code_path (generated on-demand)
- total_contributions (running total)
- contribution_count (number of contributions)
- available_balance (funds available for withdrawal)
- withdrawn_amount (total withdrawn)
- timestamps (created_at, updated_at)
```

---

## 4. Public Contribution Flow

### 4.1 Discovering Money Boxes

**Public Browse Flow:**
1. Anyone visits `/browse` (no authentication required)
2. System displays all public, active money boxes
3. Filters available:
   - Category filter
   - Search by title/description
   - Sort by: newest, ending soon, most contributed
4. User clicks on money box card
5. Redirected to contribution page

**Direct Link Flow:**
1. User receives direct link: `/box/{slug}`
2. Works for both public and private money boxes
3. Private boxes only accessible via direct link

**QR Code Flow:**
1. User scans QR code with mobile device
2. Redirected to `/box/{slug}`
3. Mobile-optimized contribution form displayed

### 4.2 Making a Contribution

**Flow:** Discover Money Box → Contribution Form → Payment → Confirmation

**Step-by-step Process:**

1. **View Money Box Page** (`/box/{slug}`)
   - Display money box details:
     - Title, description, images
     - Owner name
     - Category
     - Goal progress (if goal set)
     - Amount raised so far
     - Number of contributors
     - Time remaining (if end date set)
     - Recent contributions (non-anonymous)
   
2. **Validate Availability**
   - System checks if money box is active
   - Checks if within start/end date range
   - If inactive: displays message, disables contribution

3. **Contribution Form**
   - **Amount Field:**
     - If fixed amount: display fixed amount, no input
     - If variable/min/max/range: input field with validation
     - Real-time validation feedback
   
   - **Identity Fields** (based on contributor_identity rule):
     - Name (required if must_identify)
     - Email (required if must_identify)
     - Phone (optional)
     - "Contribute Anonymously" checkbox (if allowed)
   
   - **Optional Message:**
     - Text area for personal message (max 500 characters)
   
   - **Payment Provider Selection:**
     - Currently: TrendiPay (default)
     - Future: Multiple provider options

4. **Form Validation**
   - Amount validation:
     - Matches fixed amount (if fixed)
     - Meets minimum requirement
     - Does not exceed maximum
     - Falls within range
   - Identity validation (if required)
   - Email format validation

5. **Submit Contribution**
   - User clicks "Contribute Now"
   - System creates pending contribution record:
     ```
     - money_box_id
     - contributor_name (or null if anonymous)
     - contributor_email (or null if anonymous)
     - contributor_phone (optional)
     - amount
     - currency_code
     - is_anonymous (boolean)
     - message
     - payment_provider: 'trendipay'
     - payment_status: 'pending'
     - payment_reference (unique)
     - ip_address
     - user_agent
     - created_at timestamp
     ```

6. **Payment Initialization**
   - System calls TrendiPay API to initialize payment
   - Funds will be held by TrendiPay (not MyPiggyBox)
   - TrendiPay returns checkout URL

7. **Redirect to Payment Gateway**
   - User redirected to TrendiPay checkout page
   - User completes payment:
     - Mobile Money (MTN, Vodafone, AirtelTigo)
     - Card payment
     - Bank transfer
   - Payment processed by TrendiPay
   - **Funds held in TrendiPay merchant account**

8. **Payment Callback**
   - After payment, TrendiPay redirects to callback URL
   - System verifies payment status with TrendiPay API
   - Updates contribution record:
     - payment_status: 'completed' or 'failed'
     - transaction_rrn (reference number)
     - payment_metadata (response details)

9. **Webhook Confirmation** (Server-to-Server)
   - TrendiPay sends webhook notification
   - System validates webhook signature
   - Confirms payment status
   - Updates money box statistics:
     - total_contributions += amount
     - contribution_count += 1
     - **available_balance += (amount - platform_fee)**
   - Platform fee deducted automatically (e.g., 2.5% + processing fees)

10. **Success Page**
    - Thank you message displayed
    - Contribution receipt shown
    - Share buttons available
    - Option to view money box page

**Data Security Measures:**
- No credit card data stored on MyPiggyBox servers
- Payment processing handled entirely by TrendiPay (PCI-DSS compliant)
- Payment reference is unique and non-guessable (UUID)
- Webhook signatures validated to prevent fraud
- IP address logged for fraud detection
- Transaction metadata encrypted in database

**Financial Flow:**
- Contributor pays amount → TrendiPay holds funds
- MyPiggyBox calculates: Net Amount = Amount - Platform Fee - Payment Processing Fee
- available_balance updated with net amount
- Funds remain with TrendiPay until withdrawal requested

---

## 5. Money Box Owner Flow

### 5.1 Dashboard Overview

**Flow:** Login → Dashboard → Money Box Management

**Dashboard Features:**
1. **Statistics Summary:**
   - Total money boxes created
   - Total contributions received
   - Total amount raised (gross)
   - **Total available balance (net, withdrawable)**
   - **Total withdrawn amount**
   - Active money boxes count

2. **Money Box List:**
   - Grid/list view of all owned money boxes
   - Quick stats per box:
     - Amount raised (gross)
     - **Available balance (withdrawable)**
     - Number of contributions
     - Progress percentage (if goal set)
     - Status (active/ended)
   - Quick actions: View, Edit, Share, Statistics, **Withdraw**, Delete

3. **Recent Contributions:**
   - Last 10 contributions across all money boxes
   - Contributor name (if not anonymous)
   - Amount
   - Money box title
   - Timestamp

4. **Financial Overview Widget:**
   - Total raised across all money boxes
   - **Total available for withdrawal**
   - Total withdrawn to date
   - **"Withdraw Funds" button (if balance available)**

### 5.2 Viewing Money Box Statistics

**Flow:** Dashboard → Select Money Box → Statistics Page

**Available Data:**
1. **Overview Metrics:**
   - Total raised (gross)
   - **Available balance (withdrawable)**
   - **Withdrawn amount**
   - Number of contributions
   - Average contribution amount
   - Goal progress percentage
   - Days remaining (if end date set)

2. **Contribution History:**
   - Full list of all contributions
   - Filterable by:
     - Date range
     - Amount range
     - Payment status
   - Sortable by: date, amount
   - Shows:
     - Contributor name (or "Anonymous")
     - Amount (gross)
     - Net amount (after fees)
     - Date/time
     - Payment status
     - Message (if provided)

3. **Financial Breakdown:**
   - Total contributions: GH₵ 1,000.00
   - Platform fees: GH₵ 25.00 (2.5%)
   - Payment processing fees: GH₵ 15.00
   - **Available balance: GH₵ 960.00**
   - Previously withdrawn: GH₵ 500.00
   - **Current withdrawable: GH₵ 460.00**

4. **Analytics:**
   - Contribution timeline (if charts implemented)
   - Peak contribution times
   - Average contribution amount
   - Anonymous vs. identified contributors ratio

5. **Export Options:** (if implemented)
   - Download as CSV
   - Generate PDF report

### 5.3 Sharing Money Box

**Flow:** Money Box Page → Share Options

**Sharing Methods:**
1. **Direct Link:**
   - Unique URL: `https://domain.com/box/{slug}`
   - Copy to clipboard button
   - Works for public and private boxes

2. **QR Code:**
   - Generate QR code button (on-demand)
   - Download as PNG
   - Print-ready format
   - Embedded link to contribution page

3. **Social Media:**
   - **WhatsApp:** Pre-formatted message with link
   - **Facebook:** Share dialog with link
   - **Twitter:** Tweet with title and link

4. **Email:** (if implemented)
   - Send invitation email
   - Customizable message

### 5.4 Editing Money Box

**Flow:** Dashboard → Select Money Box → Edit → Save

**Editable Fields:**
- Title, description
- Category
- Visibility (public/private)
- Goal amount
- End date (can extend or set)
- Active status (enable/disable contributions)

**Non-Editable Fields:**
- Contribution amount rules (locked after first contribution)
- Contributor identity rules (locked after first contribution)
- Currency (locked at creation)
- Start date (cannot change past dates)

**Reason for Restrictions:**
- Prevents changing rules mid-campaign
- Maintains consistency for existing contributors
- Prevents fraud or confusion

### 5.5 Deleting Money Box

**Flow:** Money Box Page → Delete → Confirmation → Soft Delete

**Process:**
1. User clicks "Delete" button
2. Confirmation modal appears:
   - Warning about permanent action
   - Shows number of contributions
   - Shows total amount raised
   - **Shows available balance (if any)**
   - **Warning: Available balance will be transferred to user's main account**
3. User confirms deletion
4. System performs soft delete:
   - money_box.deleted_at = current timestamp
   - Box no longer visible publicly
   - Owner can still view in archive
   - Contributions preserved (for financial records)
   - **Available balance transferred to user's main wallet**
5. Success message displayed
6. User redirected to dashboard

**Financial Implications:**
- Deleted money boxes cannot accept new contributions
- Existing contributions are not affected
- Available balance remains withdrawable
- No refunds processed automatically

---

## 6. Identity Verification & Withdrawal Flow

### 6.1 Know Your Customer (KYC) Verification

**Regulatory Requirement:**
- All users must complete KYC verification before withdrawing funds
- Required for compliance with Ghana's Anti-Money Laundering regulations
- One-time verification process per user account

**Flow:** Dashboard → Withdraw Funds → KYC Required → Identity Verification → Approved → Withdrawal Enabled

**Step-by-step Process:**

**Step 1: Withdrawal Request Trigger**
1. User clicks "Withdraw Funds" button on dashboard or money box page
2. System checks KYC status in user record:
   - If `kyc_status = 'verified'`: Proceed to withdrawal form
   - If `kyc_status = 'unverified'`: Redirect to KYC verification page
   - If `kyc_status = 'pending'`: Show "Verification in Progress" message
   - If `kyc_status = 'rejected'`: Show rejection reason and retry option

**Step 2: KYC Verification Form** (`/settings/kyc-verification`)
User must provide:

1. **Personal Information:**
   - Full legal name (as on ID)
   - Date of birth
   - Phone number
   - Residential address
   - City/Town
   - Region

2. **Ghana National ID Verification:**
   - **ID Type Selection:**
     - Ghana Card (National Identification Card) - Preferred
     - Voter's ID Card
     - Passport
     - Driver's License
   
   - **ID Number:** User enters ID number
   
   - **ID Document Upload:**
     - Front of ID (clear photo or scan)
     - Back of ID (if applicable)
     - File formats: JPG, PNG, PDF
     - Max file size: 5MB per file
     - Minimum resolution: 600x400 pixels

3. **Selfie Verification:**
   - User uploads a selfie holding their ID card
   - Purpose: Prevent identity theft
   - Requirements:
     - Face clearly visible
     - ID card details readable
     - Both in same photo
     - Good lighting

4. **Bank Account Information** (for withdrawals):
   - Bank name (dropdown of Ghana banks)
   - Account number
   - Account holder name (must match ID name)
   - Branch (optional)

**Step 3: Document Review & Verification**
- User submits KYC form
- System performs initial validation:
  - All required fields completed
  - ID number format validation (Ghana Card format: GHA-XXXXXXXXX-X)
  - File size and format checks
  - Image quality check (not too blurry)
- User's `kyc_status` set to `'pending'`
- Verification ticket created in system

**Step 4: Manual Review Process**
1. **Automated Checks:**
   - ID number format validation
   - Duplicate ID check (prevent multiple accounts with same ID)
   - Image quality assessment
   - Name matching (ID vs. account name)
   - Bank account validation (format check)

2. **Manual Review by Compliance Team:**
   - Verify ID documents are authentic (not fake/edited)
   - Verify selfie matches ID photo
   - Verify bank account holder name matches ID name
   - Check for red flags:
     - Inconsistent information
     - Suspicious patterns
     - Blocked IDs list
   
3. **Review Timeline:**
   - Standard processing: 24-48 hours
   - Peak times: Up to 72 hours
   - User notified of delays

**Step 5: Verification Decision**

**If Approved:**
- `kyc_status` set to `'verified'`
- `kyc_verified_at` timestamp recorded
- Email notification sent to user
- Bank account stored securely (encrypted)
- User can now initiate withdrawals

**If Rejected:**
- `kyc_status` set to `'rejected'`
- Rejection reason recorded:
  - "ID document unclear or unreadable"
  - "Selfie does not match ID photo"
  - "Name mismatch between ID and account"
  - "Invalid or fake ID document"
  - "Bank account details invalid"
- Email notification with rejection reason
- User can resubmit after correcting issues
- Maximum 3 attempts; after that, manual support contact required

**Data Stored:**
```
users table:
- kyc_status: enum('unverified', 'pending', 'verified', 'rejected')
- kyc_submitted_at: timestamp
- kyc_verified_at: timestamp
- kyc_rejection_reason: text (if rejected)

kyc_documents table:
- user_id
- id_type: enum('ghana_card', 'voters_id', 'passport', 'drivers_license')
- id_number: string (encrypted)
- id_front_path: string (file path)
- id_back_path: string (file path, nullable)
- selfie_path: string (file path)
- bank_name: string
- bank_account_number: string (encrypted)
- bank_account_holder: string
- verification_status: enum('pending', 'approved', 'rejected')
- verified_by: admin_user_id (nullable)
- verified_at: timestamp (nullable)
- rejection_reason: text (nullable)
- timestamps
```

**Security Measures:**
- ID numbers encrypted at rest
- Bank account numbers encrypted at rest
- Document files stored in secure, non-public storage
- Access to KYC documents restricted to compliance team only
- Audit trail of all verification actions
- Documents auto-deleted after 7 years (regulatory requirement)

### 6.2 Withdrawal Process

**Flow:** Dashboard → Withdraw Funds → Select Amount → Confirm → TrendiPay Transfer → Bank Account Credit

**Prerequisites:**
- User must be KYC verified (`kyc_status = 'verified'`)
- Available balance > minimum withdrawal amount (e.g., GH₵ 10.00)
- Bank account on file

**Step-by-step Process:**

**Step 1: Initiate Withdrawal**
1. User navigates to withdrawal page:
   - From dashboard "Withdraw Funds" button
   - From money box statistics page "Withdraw" button
   - From `/wallet/withdraw` direct URL
2. System displays withdrawal form with:
   - **Available balance:** GH₵ 500.00
   - **Minimum withdrawal:** GH₵ 10.00
   - **Maximum withdrawal:** Available balance
   - **Bank account on file:** **** **** 1234 (last 4 digits)

**Step 2: Withdrawal Form**
User provides:
1. **Withdrawal Amount:**
   - Input field with validation
   - Must be ≥ minimum (GH₵ 10.00)
   - Must be ≤ available balance
   - Currency: Auto-populated (e.g., GHS)

2. **Select Source** (if multiple money boxes):
   - Dropdown of money boxes with available balance
   - OR "Withdraw from all" option (distributes from each proportionally)

3. **Bank Account Confirmation:**
   - Display bank name and account number (masked)
   - "Use different account" link → Update KYC info
   - For security: User cannot directly edit; must update KYC

4. **Withdrawal Fee Disclosure:**
   - Withdrawal amount: GH₵ 500.00
   - Transfer fee (TrendiPay): GH₵ 5.00 (1%)
   - **You will receive: GH₵ 495.00**
   - Estimated arrival: 1-3 business days

5. **Purpose of Withdrawal** (optional, for compliance):
   - Dropdown: Personal use, Business expense, Savings, Other
   - Helps with AML monitoring

**Step 3: Confirmation**
1. User clicks "Submit Withdrawal Request"
2. Confirmation modal appears:
   - Review all details
   - Amount breakdown
   - Bank account
   - Arrival estimate
   - "I confirm this information is correct" checkbox
3. User confirms

**Step 4: System Processing**
1. **Create Withdrawal Record:**
   ```
   withdrawals table:
   - user_id
   - money_box_id (nullable, if specific box)
   - amount (requested)
   - transfer_fee
   - net_amount (amount - transfer_fee)
   - currency_code
   - bank_name
   - bank_account_number (encrypted)
   - bank_account_holder
   - withdrawal_status: 'pending'
   - payment_provider: 'trendipay'
   - payment_reference: (unique UUID)
   - purpose (optional)
   - ip_address
   - requested_at: timestamp
   - processed_at: null
   - completed_at: null
   ```

2. **Update Money Box Balance:**
   - Deduct amount from `available_balance`
   - Add amount to `withdrawn_amount`
   - Record in transaction log

3. **Call TrendiPay Transfer API:**
   - **Endpoint:** TrendiPay Transfer API (NOT disclosed in this document)
   - **Request Payload:**
     - Amount (net amount after fee)
     - Currency (GHS)
     - Recipient bank account details (from KYC)
     - Unique transfer reference
     - Reason: "MyPiggyBox withdrawal"
   
4. **TrendiPay Processing:**
   - TrendiPay validates recipient account
   - Initiates bank transfer
   - Returns transfer status:
     - `pending`: Transfer initiated, awaiting bank processing
     - `processing`: Bank is processing
     - `completed`: Funds credited to account
     - `failed`: Transfer failed (e.g., invalid account)

**Step 5: Status Updates**
1. **Initial Status: Pending**
   - User sees "Withdrawal pending" on dashboard
   - Email notification: "Your withdrawal request is being processed"

2. **Status: Processing** (received from TrendiPay webhook)
   - System updates: `withdrawal_status = 'processing'`
   - Email notification: "Your withdrawal is being transferred"

3. **Status: Completed** (received from TrendiPay webhook)
   - System updates:
     - `withdrawal_status = 'completed'`
     - `completed_at = current timestamp`
   - Email notification: "Your withdrawal has been completed. Funds should arrive in 1-3 business days."
   - User dashboard updated

4. **Status: Failed** (received from TrendiPay webhook)
   - System updates:
     - `withdrawal_status = 'failed'`
     - `failure_reason = (from TrendiPay response)`
   - **Refund to available_balance** (amount returned to money box)
   - Email notification: "Your withdrawal failed. Reason: [reason]. Funds returned to your balance."
   - User can retry withdrawal

**Step 6: Bank Account Credit**
1. Bank processes transfer from TrendiPay
2. Funds appear in user's bank account
3. Timeline: 1-3 business days (depending on bank)
4. User receives bank notification (from their bank)

**Withdrawal Limitations:**
- **Minimum amount:** GH₵ 10.00 (or equivalent in other currencies)
- **Maximum amount:** Available balance
- **Frequency limit:** 3 withdrawals per day (anti-fraud measure)
- **Daily limit:** GH₵ 10,000 per day (configurable, AML compliance)
- **Monthly limit:** GH₵ 50,000 per month (configurable, AML compliance)

**Withdrawal Fees:**
- **Transfer fee:** 1% of withdrawal amount (minimum GH₵ 2.00, maximum GH₵ 20.00)
- **Fee charged by:** TrendiPay (covers bank transfer costs)
- **Fee disclosed:** Before user confirms withdrawal
- **Fee deducted from:** Withdrawal amount (user receives net amount)

**Example Calculation:**
```
User requests withdrawal: GH₵ 500.00
Transfer fee (1%): GH₵ 5.00
User receives: GH₵ 495.00

Deducted from available_balance: GH₵ 500.00
Added to withdrawn_amount: GH₵ 500.00
```

### 6.3 Withdrawal History & Tracking

**Flow:** Dashboard → Wallet/Withdrawals → View History

**Available Information:**
1. **Withdrawal List:**
   - All withdrawal requests (past and pending)
   - Filterable by:
     - Status (pending, processing, completed, failed)
     - Date range
     - Money box (if applicable)
   - Sortable by: date, amount

2. **Withdrawal Details:**
   - Withdrawal ID (for support reference)
   - Date/time requested
   - Amount (gross)
   - Transfer fee
   - Net amount received
   - Bank account (masked)
   - Status with timestamp
   - Estimated arrival (if pending/processing)
   - Failure reason (if failed)

3. **Downloadable Receipts:**
   - PDF receipt per withdrawal
   - Includes:
     - MyPiggyBox transaction details
     - TrendiPay transfer reference
     - Bank account details
     - Timestamps
     - Support contact

### 6.4 Failed Withdrawal Handling

**Common Failure Reasons:**
1. **Invalid bank account:**
   - Resolution: User updates bank account in KYC settings
   - Funds returned to available_balance

2. **Insufficient funds in TrendiPay merchant account:**
   - Resolution: MyPiggyBox tops up merchant account
   - Withdrawal automatically retried

3. **Bank system downtime:**
   - Resolution: Automatic retry after delay
   - User notified of delay

4. **Suspected fraud (flagged by TrendiPay):**
   - Resolution: Manual review by compliance team
   - User may need to provide additional verification
   - Withdrawal approved or permanently rejected

**User Support:**
- Failed withdrawals trigger automatic support ticket
- User can contact support with Withdrawal ID
- Compliance team investigates within 24 hours
- Funds always returned to balance if permanently failed

---

## 7. Payment Processing & Financial Flows

### 7.1 Payment Provider Integration (TrendiPay)

**Architecture:**
- MyPiggyBox uses Payment Manager pattern
- TrendiPay is the default provider
- Extensible for future providers (Stripe, Flutterwave, etc.)

**MyPiggyBox Financial Model:**
- **MyPiggyBox does NOT hold user funds at any point**
- All funds held by TrendiPay in merchant account
- MyPiggyBox acts as facilitator/platform only
- Withdrawals processed via TrendiPay Transfer API
- User funds segregated in TrendiPay merchant account (if supported)

**Fund Flow Diagram:**
```
Contributor 
    ↓ (pays amount)
TrendiPay Payment Gateway
    ↓ (holds funds)
TrendiPay Merchant Account (MyPiggyBox)
    ↓ (upon withdrawal request)
TrendiPay Transfer API
    ↓ (bank transfer)
User's Bank Account
```

### 7.2 Platform Fee Structure

**Contribution Fees:**
- **Platform fee:** 2.5% of contribution amount
- **Payment processing fee:** Charged by TrendiPay (varies by payment method)
  - Mobile Money: ~1.5%
  - Card: ~2.9% + GH₵ 0.30
  - Bank transfer: ~GH₵ 2.00 flat
- **Total deducted from contribution:** Platform fee + Payment processing fee
- **User sees:** Net amount added to available_balance

**Example:**
```
Contributor donates: GH₵ 100.00
Platform fee (2.5%): GH₵ 2.50
Payment processing (1.5%): GH₵ 1.50
Total fees: GH₵ 4.00
Available to withdraw: GH₵ 96.00
```

**Withdrawal Fees:**
- **Transfer fee:** 1% of withdrawal (min GH₵ 2, max GH₵ 20)
- Covers bank transfer costs
- Deducted from withdrawal amount

**Fee Transparency:**
- All fees disclosed before transaction
- Breakdown shown in statistics page
- Detailed in user agreement

### 7.3 Financial Reconciliation

**Daily Reconciliation Process:**
1. System generates daily report of all completed contributions
2. Reports include:
   - Money box ID and title
   - Contribution ID
   - Payment reference
   - Amount (gross)
   - Platform fee
   - Payment processing fee
   - Net amount (to available_balance)
   - Transaction RRN
   - Timestamp
3. Report compared against TrendiPay settlement reports
4. Discrepancies flagged for manual review

**Withdrawal Reconciliation:**
1. Daily report of all completed withdrawals
2. Cross-referenced with TrendiPay transfer confirmations
3. Bank account credits verified (where possible)
4. Failed transfers investigated

**Data Retention:**
- All contribution records retained indefinitely
- All withdrawal records retained indefinitely
- Payment metadata stored in encrypted format
- Audit trail of all payment and withdrawal status changes
- Webhook logs retained for 90 days
- Financial reports archived for 7 years (regulatory requirement)

---

## 8. User Roles & Permissions

### 8.1 Guest (Unauthenticated) Users
**Can:**
- View public money boxes (`/browse`, `/box/{slug}`)
- Make contributions to any money box (public or via direct link)
- Search and filter public money boxes
- View static pages (about, terms, privacy)

**Cannot:**
- Create money boxes
- View dashboard or statistics
- Edit money boxes
- Access owner-only features
- Withdraw funds

### 8.2 Registered Users (Authenticated & Verified)
**Can:**
- All guest capabilities, plus:
- Create unlimited money boxes
- Edit own money boxes
- View statistics for own money boxes
- Delete own money boxes
- Generate QR codes
- Access sharing tools
- View personal dashboard
- Manage account settings
- Enable two-factor authentication
- **View available balance**
- **Submit KYC verification** (required for withdrawals)

**Cannot (until KYC verified):**
- Withdraw funds
- Update bank account
- Request fund transfers

### 8.3 KYC Verified Users
**Can:**
- All registered user capabilities, plus:
- **Initiate withdrawals**
- **View withdrawal history**
- **Update bank account information** (with re-verification)
- **Track fund transfers**

**Cannot:**
- Edit or delete other users' money boxes
- View private statistics of other users' money boxes
- Access admin features (if applicable)

### 8.4 Money Box Ownership
**Authorization Rules:**
- Users can only view/edit/delete their own money boxes
- Enforced via Laravel Policy: `MoneyBoxPolicy`
- Authorization checks on all CRUD operations
- Failed authorization returns 403 Forbidden

**Policy Logic:**
```php
// View: User must own the money box OR money box is public
public function view(User $user, MoneyBox $moneyBox): bool
{
    return $moneyBox->user_id === $user->id 
        || $moneyBox->visibility === Visibility::Public;
}

// Update/Delete: User must own the money box
public function update(User $user, MoneyBox $moneyBox): bool
{
    return $moneyBox->user_id === $user->id;
}

// Withdraw: User must own money box AND be KYC verified
public function withdraw(User $user, MoneyBox $moneyBox): bool
{
    return $moneyBox->user_id === $user->id 
        && $user->kyc_status === 'verified';
}
```

---

## 9. Data Privacy & Security

### 9.1 Personal Data Collection

**User Registration Data:**
- Name
- Email address
- Country
- IP address (for security)
- User agent (browser/device info)

**KYC Verification Data:**
- Full legal name
- Date of birth
- Phone number
- Residential address
- National ID type and number
- ID document images (front, back)
- Selfie with ID
- Bank account details (bank name, account number, holder name)

**Contributor Data:**
- Name (optional if anonymous allowed)
- Email (optional if anonymous allowed)
- Phone (optional)
- Message (optional)
- IP address (fraud detection)
- User agent

**Payment Data:**
- Payment reference (generated by system)
- Transaction RRN (from TrendiPay)
- Payment status
- Payment method (e.g., mobile_money, card)
- **NOT STORED:** Credit card numbers, CVV, PIN codes

**Withdrawal Data:**
- Withdrawal amount
- Bank account (encrypted)
- Transfer reference
- Transfer status
- IP address (for security)

### 9.2 Data Protection Measures

**Encryption:**
- All data transmitted over HTTPS (TLS 1.2+)
- Database connections encrypted
- Payment metadata encrypted at rest
- **ID numbers encrypted at rest (AES-256)**
- **Bank account numbers encrypted at rest (AES-256)**
- Passwords hashed with bcrypt (cost factor 12)

**Access Control:**
- Role-based access control (RBAC)
- Policy-based authorization
- Session-based authentication
- CSRF protection on all forms
- XSS protection (content sanitization)
- **KYC documents access restricted to compliance team only**
- **Audit trail of all KYC document accesses**

**Anonymity Features:**
- Contributors can choose to remain anonymous
- Anonymous contributions display as "Anonymous"
- No personal data collected for anonymous contributions
- IP address still logged for fraud detection (not displayed publicly)

**Document Security:**
- KYC documents stored in secure, non-public storage (AWS S3 private bucket or encrypted disk)
- Access requires authentication + specific permissions
- All document accesses logged
- Documents auto-deleted after 7 years (regulatory compliance)

### 9.3 Data Retention

**User Accounts:**
- Active accounts retained indefinitely
- Deleted accounts (if feature implemented): 30-day soft delete, then permanent
- **KYC documents retained for 7 years after account closure** (regulatory)

**Money Boxes:**
- Soft deleted money boxes retained for 90 days
- After 90 days: can be permanently deleted or archived

**Contributions:**
- All contributions retained indefinitely for financial audit purposes
- Even if money box is deleted, contributions preserved

**Withdrawals:**
- All withdrawal records retained indefinitely
- Required for tax reporting and audits

**Logs:**
- Payment webhooks: 90 days
- Error logs: 30 days
- Audit logs: 1 year
- **KYC verification logs: 7 years**
- **Financial transaction logs: 7 years**

### 9.4 GDPR Compliance (if applicable)

**User Rights:**
1. **Right to Access:** Users can export their data (if implemented)
2. **Right to Rectification:** Users can update account information
3. **Right to Erasure:** Users can request account deletion
   - **Exception:** Financial records retained for 7 years (legal obligation)
   - **Exception:** KYC documents retained for 7 years (regulatory requirement)
4. **Right to Data Portability:** Export functionality (if implemented)
5. **Right to Object:** Users can opt-out of marketing (if applicable)

**Legal Basis for Processing:**
- Account data: Contract necessity
- Contribution data: Legitimate interest (fraud prevention)
- **KYC data: Legal obligation (AML/CFT compliance)**
- **Financial data: Legal obligation (tax and regulatory compliance)**
- Marketing data: Consent (if applicable)

### 9.5 Cookie Policy

**Cookies Used:**
1. **Session Cookie:** (Required)
   - Name: `laravel_session`
   - Purpose: Maintain user authentication
   - Expiry: 2 hours (or "Remember Me": 30 days)
   - HTTP-only: Yes
   - Secure: Yes (production)

2. **CSRF Token:** (Required)
   - Name: `XSRF-TOKEN`
   - Purpose: Prevent cross-site request forgery
   - Expiry: Session
   - HTTP-only: No
   - Secure: Yes (production)

3. **Analytics Cookies:** (if implemented)
   - Purpose: Track site usage
   - Requires consent banner

---

## 10. Fraud Prevention & Risk Management

### 10.1 Fraud Detection Measures

**Contribution Level:**
- IP address logging
- Rate limiting (max contributions per IP per hour)
- Duplicate transaction detection
- Unusual amount detection (e.g., extremely large contributions)
- Payment reference uniqueness validation
- Velocity checks (multiple contributions from same user/IP in short time)

**Account Level:**
- Email verification required
- Rate limiting on account creation
- Unusual activity detection (e.g., creating 100 money boxes in 1 hour)
- Two-factor authentication (optional)
- **KYC verification required for withdrawals**
- **Duplicate ID detection (prevents multiple accounts with same ID)**

**Withdrawal Level:**
- **Daily withdrawal limits (GH₵ 10,000)**
- **Monthly withdrawal limits (GH₵ 50,000)**
- **Frequency limits (3 withdrawals per day)**
- **Minimum withdrawal amount (GH₵ 10)**
- **Bank account name must match KYC name**
- Unusual withdrawal pattern detection (e.g., immediately after large contribution)
- Manual review triggered for:
  - First withdrawal over GH₵ 1,000
  - Withdrawals to new bank account
  - Multiple failed withdrawal attempts

**Payment Level:**
- Webhook signature verification
- Payment status verification via API (double-check)
- Transaction reference validation
- Amount verification (matches original request)
- TrendiPay fraud detection systems

### 10.2 Anti-Money Laundering (AML) Compliance

**KYC Requirements:**
- National ID verification mandatory for withdrawals
- Bank account verification (name matching)
- Address verification via ID document
- Selfie verification (liveness check)

**Transaction Monitoring:**
- Automated monitoring of:
  - Large transactions (> GH₵ 5,000)
  - Rapid movement of funds (contributed then withdrawn quickly)
  - Structuring (multiple transactions just under reporting threshold)
  - Unusual patterns (e.g., round-robin contributions)
- Flagged transactions reviewed by compliance team

**Reporting Obligations:**
- Suspicious Activity Reports (SARs) filed with Ghana Financial Intelligence Centre (FIC)
- Threshold: GH₵ 20,000 in single transaction or related transactions
- Large Cash Transaction Reports (LCTRs) for applicable transactions
- Record-keeping for 7 years

**Enhanced Due Diligence:**
- Applied to:
  - Politically Exposed Persons (PEPs)
  - High-risk countries
  - High-value money boxes (> GH₵ 50,000 goal)
- Requires additional documentation and approval

### 10.3 Dispute Resolution

**Contributor Disputes:**
1. Contributor contacts money box owner directly (email provided)
2. If unresolved, contributor contacts MyPiggyBox support
3. MyPiggyBox facilitates communication
4. Refunds processed through TrendiPay (if applicable)
5. Money box owner responsible for refund decision
6. **Refunded amounts deducted from available_balance**

**Withdrawal Disputes:**
1. User reports withdrawal issue (not received, wrong amount, etc.)
2. MyPiggyBox verifies withdrawal status with TrendiPay
3. If issue with TrendiPay transfer: Escalate to TrendiPay
4. If issue with user's bank: User contacts bank with reference
5. MyPiggyBox provides documentation for dispute

**Owner Disputes:**
1. Owner reports suspicious contribution
2. MyPiggyBox investigates (IP, payment details)
3. If fraudulent: contribution flagged, owner notified
4. Payment provider notified for chargeback/reversal
5. **If chargeback occurs: Amount deducted from available_balance**

**Platform Responsibilities:**
- MyPiggyBox is a facilitator, not a payment processor
- TrendiPay handles actual fund transfers
- MyPiggyBox provides transaction records for disputes
- Final refund/chargeback decisions made by payment provider
- **MyPiggyBox not liable for failed bank transfers (TrendiPay responsibility)**

### 10.4 Prohibited Uses

**Money boxes cannot be created for:**
- Illegal activities
- Fraudulent schemes (e.g., pyramid schemes, Ponzi schemes)
- Hate speech or discrimination
- Adult content or services
- Gambling or betting
- Sale of regulated goods (weapons, drugs, etc.)
- Money laundering or terrorist financing
- Tax evasion
- Circumventing sanctions

**Withdrawal Restrictions:**
- Cannot withdraw to third-party bank accounts (must match KYC name)
- Cannot withdraw if KYC verification rejected
- Cannot withdraw if under investigation for fraud
- Cannot withdraw if account suspended

**Enforcement:**
- User agreement acceptance required on registration
- Reported money boxes reviewed by platform
- Violating money boxes disabled immediately
- Available balance frozen during investigation
- Repeat offenders banned
- **Suspicious activity reported to authorities**

---

## 11. Money Box "Piggy-Someone" Feature

### 11.1 Overview
The "Piggy-Someone" feature allows contributors to donate to a specific user's personal piggy box using a unique code, without needing to browse or search.

### 11.2 User Flow

**Step 1: Accessing the Feature**
- User visits `/piggy-someone` (public route)
- Page displays a form: "Enter Code to Contribute"

**Step 2: Code Entry**
- User enters a unique piggy code (e.g., `USER123`)
- Clicks "Find Piggy Box"

**Step 3: Code Lookup**
- System searches for user by `piggy_code`
- If found: User's personal piggy box displayed
- If not found: Error message shown

**Step 4: Contribution**
- User redirected to `/piggy/{code}`
- Contribution form displayed (similar to regular money box)
- Follows standard payment flow
- After payment: Callback to `/piggy/callback`
- **Funds added to user's available_balance (after fees)**

**Step 5: Withdrawal**
- User with piggy code can withdraw from personal piggy box
- Same withdrawal process as regular money boxes
- Requires KYC verification

### 11.3 Technical Details

**Routes:**
```php
Route::get('/piggy-someone', [PiggyBoxController::class, 'lookup'])
    ->name('piggy.lookup');
Route::post('/piggy-someone/find', [PiggyBoxController::class, 'findByCode'])
    ->name('piggy.find');
Route::get('/piggy/{code}', [PiggyBoxController::class, 'showByCode'])
    ->name('piggy.show');
Route::post('/piggy/{user}/donate', [PiggyBoxController::class, 'donate'])
    ->name('piggy.donate');
Route::get('/piggy/callback', [PiggyBoxController::class, 'callback'])
    ->name('piggy.callback');
```

**User Model Extension:**
- `piggy_code` field: unique, alphanumeric code per user
- Generated on registration or first piggy-someone access
- Associated with user's default/personal piggy box

**Security:**
- Code is unique and non-guessable
- Rate limiting on code lookup attempts
- No enumeration protection (doesn't reveal if code exists)
- **Withdrawal requires KYC even for piggy-someone donations**

---

## 12. Technical Architecture Summary

### 12.1 Technology Stack

**Backend:**
- Framework: Laravel 12
- Language: PHP 8.2+
- Database: MySQL / PostgreSQL / SQLite
- Authentication: Laravel Fortify
- Queue: Redis / Database (for async jobs)

**Frontend:**
- Templates: Blade (Laravel)
- Styling: Tailwind CSS 4
- JavaScript: Alpine.js (lightweight interactivity)
- Components: Flux UI components
- Notifications: SweetAlert2
- Icons: Heroicons

**Third-Party Services:**
- Payment: TrendiPay API (payment collection + transfers)
- QR Codes: endroid/qr-code library
- Media: Spatie Media Library
- Email: SMTP / AWS SES / Mailgun (configurable)
- **ID Verification:** Manual review (future: automated OCR/face recognition)

### 12.2 Design Patterns

**1. Action Pattern:**
- Single-purpose action classes for business logic
- Examples: `CreateMoneyBoxAction`, `ProcessContributionAction`, **`ProcessWithdrawalAction`**
- Fire events upon completion

**2. Manager Pattern:**
- `PaymentManager` for payment provider abstraction
- Easily switch between TrendiPay, Stripe, etc.

**3. Event-Driven Architecture:**
- Events: `MoneyBoxCreated`, `ContributionProcessed`, **`WithdrawalRequested`, `WithdrawalCompleted`, `KYCSubmitted`, `KYCVerified`**
- Listeners: Email notifications, stats updates, **compliance alerts**

**4. Policy Pattern:**
- Authorization via Laravel Policies
- Example: `MoneyBoxPolicy` for ownership checks, **`WithdrawalPolicy` for KYC verification checks**

### 12.3 Database Schema

**Key Tables:**
1. `users`: User accounts (**added:** `kyc_status`, `piggy_code`)
2. `countries`: Country and currency data
3. `categories`: Money box categories
4. `money_boxes`: Piggy box records (**added:** `available_balance`, `withdrawn_amount`)
5. `contributions`: Contribution records
6. **`withdrawals`:** Withdrawal request records
7. **`kyc_documents`:** KYC verification documents
8. `media`: File uploads (Spatie)

**Relationships:**
- User → Money Boxes (1:many)
- User → KYC Documents (1:1)
- User → Withdrawals (1:many)
- Money Box → Contributions (1:many)
- Money Box → Withdrawals (1:many)
- Country → Users (1:many)
- Category → Money Boxes (1:many)

### 12.4 API Endpoints (Internal)

**Public:**
- `GET /` - Homepage
- `GET /browse` - Browse money boxes
- `GET /box/{slug}` - View money box
- `POST /box/{slug}/contribute` - Submit contribution

**Authenticated:**
- `GET /dashboard` - User dashboard
- `GET /money-boxes` - List user's money boxes
- `POST /money-boxes` - Create money box
- `GET /money-boxes/{id}` - View money box details
- `PUT /money-boxes/{id}` - Update money box
- `DELETE /money-boxes/{id}` - Delete money box
- `GET /money-boxes/{id}/statistics` - View statistics
- `GET /money-boxes/{id}/share` - Sharing options
- **`GET /settings/kyc-verification` - KYC verification page**
- **`POST /settings/kyc-verification` - Submit KYC documents**
- **`GET /wallet/withdraw` - Withdrawal form**
- **`POST /wallet/withdraw` - Submit withdrawal request**
- **`GET /wallet/withdrawals` - Withdrawal history**

**Webhooks:**
- `PUT /webhooks/trendipay` - TrendiPay webhook (payments & transfers)

---

## 13. Regulatory Compliance Summary

### 13.1 Financial Regulations

**Money Transmitter Status:**
- MyPiggyBox does NOT hold or transmit funds
- All payments processed by licensed provider (TrendiPay)
- All withdrawals processed by TrendiPay Transfer API
- MyPiggyBox is a technology platform/facilitator only
- **Funds held in TrendiPay merchant account (segregated if supported)**

**Licensing:**
- TrendiPay holds necessary licenses for:
  - Payment processing
  - Money transmission
  - Bank transfers
- MyPiggyBox operates as technology service provider
- No additional money transmitter license required for MyPiggyBox

**Tax Implications:**
- Money box owners responsible for declaring received funds
- MyPiggyBox does not issue tax forms (e.g., 1099)
- Contribution and withdrawal records available for owner's tax purposes
- **Annual statement provided to users (if implemented)**

### 13.2 Data Protection

**Compliance:**
- GDPR compliant (if operating in EU)
- CCPA compliant (if operating in California)
- **Ghana Data Protection Act (Act 843) compliant**
- Data encryption in transit and at rest
- User consent for data processing
- Privacy policy and terms of service provided
- **KYC data processing for legal obligation (AML compliance)**

**Data Subjects' Rights:**
- Right to access personal data
- Right to correct personal data
- Right to delete account (with limitations for financial records)
- Right to data portability
- **KYC documents retained 7 years (legal obligation overrides erasure right)**

### 13.3 Anti-Money Laundering (AML)

**Current Measures:**
- **Mandatory KYC for withdrawals (Ghana national ID verification)**
- IP address logging
- Transaction limits (daily/monthly)
- Unusual activity detection
- **Withdrawal limits and monitoring**
- Cooperation with payment provider's AML processes
- **Suspicious Activity Reporting (SAR) to Ghana FIC**

**Payment Provider Responsibility:**
- TrendiPay handles payment-level KYC
- TrendiPay monitors suspicious transactions
- TrendiPay reports to financial authorities as required
- **MyPiggyBox handles platform-level KYC for withdrawals**

**Record-Keeping:**
- All KYC documents retained 7 years
- All transaction records retained 7 years
- Audit trail of verification decisions
- Compliance reports generated quarterly

### 13.4 Consumer Protection

**Transparency:**
- Clear disclosure of fees (platform fee, withdrawal fee)
- Money box details visible before contribution
- Progress tracking available
- Contribution history accessible
- **Withdrawal fee disclosed before confirmation**
- **Expected transfer timeline disclosed**

**Dispute Resolution:**
- Support contact available
- Refund policy communicated
- Payment provider handles chargebacks
- **Withdrawal dispute resolution process documented**

**User Agreement:**
- Terms of Service accepted on registration
- Prohibited uses clearly defined
- Liability limitations stated
- Dispute resolution process outlined
- **Withdrawal terms and conditions disclosed**
- **KYC requirements explained**

**Fund Protection:**
- Funds held by licensed provider (TrendiPay)
- **User balances tracked separately in MyPiggyBox database**
- Daily reconciliation with TrendiPay
- **Clear segregation of funds (where supported by TrendiPay)**

---

## 14. Operational Procedures

### 14.1 User Support

**Support Channels:**
- Email support: support@mypiggybox.com
- Help documentation (if implemented)
- FAQ page (if implemented)
- **KYC support: kyc@mypiggybox.com**

**Common Issues:**
1. Payment not processed → Check payment status, contact TrendiPay
2. Money box not visible → Check visibility settings
3. Cannot edit money box → Explain locked fields after first contribution
4. Forgot password → Send password reset link
5. **KYC verification rejected → Provide rejection reason, allow resubmission**
6. **Withdrawal not received → Check status, verify with TrendiPay, contact bank**
7. **Cannot withdraw → Verify KYC status**

### 14.2 Incident Response

**Security Incidents:**
1. Detect: Monitoring alerts trigger
2. Assess: Determine severity and impact
3. Contain: Disable affected accounts/money boxes
4. Investigate: Review logs, identify root cause
5. Remediate: Fix vulnerability, restore service
6. Notify: Inform affected users if data breach
7. **KYC data breach: Immediate notification to users and regulatory authorities**

**Payment Issues:**
1. User reports payment problem
2. Verify payment status in database
3. Check TrendiPay transaction status via API
4. If discrepancy: Contact TrendiPay support
5. Update user on resolution
6. If system error: Issue refund or credit

**Withdrawal Issues:**
1. User reports withdrawal problem
2. Verify withdrawal status in database
3. Check TrendiPay transfer status via API
4. If pending/processing: Advise user to wait
5. If failed: Investigate reason, return funds to balance
6. If TrendiPay issue: Escalate to TrendiPay support
7. If bank issue: Advise user to contact bank with reference

### 14.3 Monitoring & Alerts

**System Health:**
- Server uptime monitoring
- Database performance monitoring
- Payment API response time tracking
- **Transfer API response time tracking**
- Error rate monitoring

**Business Metrics:**
- Daily contribution volume
- New user registrations
- Active money boxes
- Payment success rate
- Average contribution amount
- **Daily withdrawal volume**
- **Withdrawal success rate**
- **KYC verification backlog**

**Alerts:**
- Payment API downtime → Immediate notification
- **Transfer API downtime → Immediate notification**
- Spike in failed payments → Investigate
- **Spike in failed withdrawals → Investigate**
- Unusual account creation rate → Check for bot activity
- Webhook signature failures → Security alert
- **High-value withdrawal (> GH₵ 5,000) → Manual review**
- **KYC verification backlog > 100 → Scale up review team**

---

## 15. Future Enhancements

### 15.1 Planned Features

1. **Email Notifications:**
   - Welcome email on registration
   - Money box creation confirmation
   - Contribution thank you email
   - Owner notification on new contribution
   - Goal milestone notifications (50%, 100%)
   - **KYC submission confirmation**
   - **KYC verification result notification**
   - **Withdrawal request confirmation**
   - **Withdrawal completion notification**

2. **Advanced Analytics:**
   - Charts and graphs (Chart.js)
   - Contribution trends over time
   - Peak contribution hours/days
   - **Withdrawal patterns and trends**
   - Donor demographics (if collected)

3. **Export Functionality:**
   - CSV export of contributions
   - **CSV export of withdrawals**
   - PDF report generation
   - QR code bulk download
   - **Tax summary report (annual)**

4. **Automated KYC Verification:**
   - OCR (Optical Character Recognition) for ID documents
   - Face recognition/liveness detection
   - Automated ID validation via Ghana Card API (if available)
   - Faster verification (minutes instead of days)

5. **Multi-Payment Providers:**
   - Stripe integration
   - Flutterwave integration
   - PayPal integration
   - User can select preferred provider

6. **Mobile App:**
   - iOS and Android apps
   - REST API for mobile clients
   - Push notifications
   - **Mobile KYC verification (selfie capture)**

7. **Admin Panel:**
   - Platform management interface
   - User management
   - Money box moderation
   - **KYC verification dashboard**
   - **Withdrawal approval/review dashboard**
   - Analytics dashboard
   - Report management

8. **Enhanced Withdrawal Features:**
   - **Scheduled withdrawals (e.g., weekly auto-withdrawal)**
   - **Multiple bank accounts per user**
   - **Instant transfer (if supported by TrendiPay)**
   - **Withdrawal to mobile money wallets**

### 15.2 Scalability Considerations

**Current Capacity:**
- Supports up to 10,000 concurrent users (estimated)
- Database optimized for 1 million records
- **KYC review capacity: ~100 verifications per day**

**Scaling Strategy:**
- Horizontal scaling: Add more web servers
- Database: Read replicas for analytics
- Caching: Redis for frequently accessed data
- CDN: For static assets and images
- Queue workers: Background job processing
- **KYC team scaling: Hire more reviewers or implement automated verification**
- **Geographic expansion: Partner with regional payment providers**

---

## 16. Glossary

**Money Box (Piggy Box):**
A digital contribution collection created by a user to raise funds for a specific purpose.

**Contributor:**
A person who makes a monetary contribution to a money box (registered user or guest).

**Money Box Owner:**
The registered user who created the money box.

**Available Balance:**
The net amount in a money box that can be withdrawn (after platform and payment processing fees).

**Withdrawn Amount:**
The total amount that has been successfully withdrawn from a money box.

**KYC (Know Your Customer):**
The process of verifying a user's identity to comply with regulatory requirements.

**Ghana Card:**
Ghana's national identification card, the preferred ID for KYC verification.

**Withdrawal:**
The process of transferring funds from available balance to user's bank account.

**Transfer Fee:**
The fee charged for processing a withdrawal (1% of withdrawal amount).

**Platform Fee:**
The fee charged by MyPiggyBox on each contribution (2.5%).

**Payment Processing Fee:**
The fee charged by TrendiPay for processing payments (varies by method).

**TrendiPay:**
The payment service provider used to process contributions and transfers.

**Slug:**
A URL-friendly unique identifier for a money box (e.g., `birthday-party-abc123`).

**Payment Reference:**
A unique identifier for a payment transaction, used to track and verify payments.

**Transfer Reference:**
A unique identifier for a withdrawal transfer, used to track bank transfers.

**Webhook:**
A server-to-server notification sent by the payment provider to confirm payment or transfer status.

**QR Code:**
A scannable code that links directly to a money box contribution page.

**Anonymous Contribution:**
A contribution where the contributor chooses not to reveal their identity.

**Goal Amount:**
The target amount the money box owner wishes to raise.

**Contribution Amount Rules:**
Rules that define how much contributors can donate (fixed, variable, min, max, range).

**Contributor Identity Rules:**
Rules that define whether contributors must identify themselves or can remain anonymous.

**Visibility:**
Whether a money box is public (listed on browse page) or private (unlisted, direct link only).

**CSRF:**
Cross-Site Request Forgery - a security attack that tricks users into performing unwanted actions.

**XSS:**
Cross-Site Scripting - a security vulnerability that allows attackers to inject malicious scripts.

**AML:**
Anti-Money Laundering - regulations to prevent money laundering and terrorist financing.

**SAR:**
Suspicious Activity Report - a report filed with financial authorities for suspicious transactions.

---

## 17. Contact Information

**Platform Name:** MyPiggyBox  
**Website:** https://mypiggybox.com (placeholder)  
**Support Email:** support@mypiggybox.com (placeholder)  
**Technical Contact:** tech@mypiggybox.com (placeholder)  
**KYC Support:** kyc@mypiggybox.com (placeholder)

**Regulatory Inquiries:**
Please direct all regulatory and compliance questions to: compliance@mypiggybox.com (placeholder)

---

## 18. Document Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | 2024-12-21 | System Documentation | Initial comprehensive user flow document with KYC and withdrawal processes |

---

**END OF DOCUMENT**

---

## Appendix A: Sample Screenshots (To Be Added)

The following screenshots should be provided for regulatory review:

1. Homepage with featured money boxes
2. Registration form
3. Money box creation form
4. Public money box contribution page
5. Payment gateway (TrendiPay) interface
6. User dashboard with available balance
7. Money box statistics page with financial breakdown
8. Sharing options page with QR code
9. **KYC verification form**
10. **KYC document upload interface**
11. **Withdrawal form with fee breakdown**
12. **Withdrawal history page**
13. Privacy policy page
14. Terms of service page

---

## Appendix B: KYC Document Requirements

**Acceptable Ghana National IDs:**
1. **Ghana Card (Preferred)**
   - Format: GHA-XXXXXXXXX-X
   - Issued by: National Identification Authority (NIA)
   - Must be valid (not expired)

2. **Voter's ID Card**
   - Issued by: Electoral Commission of Ghana
   - Must be valid

3. **Ghana Passport**
   - Must be valid (not expired)
   - Must show clear photo and details

4. **Driver's License**
   - Issued by: Driver and Vehicle Licensing Authority (DVLA)
   - Must be valid

**Document Quality Requirements:**
- Clear, legible text
- All four corners visible
- No glare or shadows
- Minimum resolution: 600x400 pixels
- File size: Under 5MB
- Formats: JPG, PNG, PDF

**Selfie Requirements:**
- Face clearly visible
- ID document held next to face
- All ID details readable
- Good lighting
- No filters or editing

---

## Appendix C: Withdrawal Fee Examples

| Withdrawal Amount | Transfer Fee (1%) | User Receives | Minimum Fee | Maximum Fee |
|-------------------|-------------------|---------------|-------------|-------------|
| GH₵ 10.00 | GH₵ 2.00* | GH₵ 8.00 | GH₵ 2.00 | - |
| GH₵ 50.00 | GH₵ 2.00* | GH₵ 48.00 | GH₵ 2.00 | - |
| GH₵ 200.00 | GH₵ 2.00 | GH₵ 198.00 | - | - |
| GH₵ 500.00 | GH₵ 5.00 | GH₵ 495.00 | - | - |
| GH₵ 1,000.00 | GH₵ 10.00 | GH₵ 990.00 | - | - |
| GH₵ 2,500.00 | GH₵ 20.00* | GH₵ 2,480.00 | - | GH₵ 20.00 |
| GH₵ 5,000.00 | GH₵ 20.00* | GH₵ 4,980.00 | - | GH₵ 20.00 |

*Minimum fee: GH₵ 2.00  
*Maximum fee: GH₵ 20.00

---

## Appendix D: Database Schema Diagram

*(To be created using database modeling tool)*

Key additions for withdrawal feature:
- `users.kyc_status`
- `users.piggy_code`
- `money_boxes.available_balance`
- `money_boxes.withdrawn_amount`
- `kyc_documents` table (new)
- `withdrawals` table (new)

---

## Appendix E: Security Certificates

*(To be provided)*
- SSL/TLS Certificate details
- PCI-DSS Compliance (via TrendiPay)
- SOC 2 Report (if applicable)
- **Data Protection Registration (Ghana Data Protection Commission)**

---

**This document is confidential and intended for regulatory review purposes only.**
