# Financial Domain Rules & Business Logic

Rule file ini mengatur seluruh domain bisnis pengelolaan keuangan, sinking funds, dan pencatatan kas untuk proyek ini.

---

## 1. Core Mathematical Formulas

### A. Accumulating Item/Goal Balance (Sinking Fund)
Formula untuk menghitung total akumulasi saldo pada detail `Item` (Goal/Sinking Fund):

$$\text{Saldo Item} = \sum \text{Income} + \sum \text{Transfer In} - \sum \text{Expense}$$

*   **Income:** Transaksi berjenis `income` yang ditujukan langsung ke `item_id`.
*   **Transfer In:** Transaksi berjenis `transfer` di mana `target_item_id` bernilai sama dengan `item_id`.
*   **Expense:** Transaksi berjenis `expense` yang memotong saldo dari `item_id`.

### B. Auto-Status Calculation
Status dari suatu `Item` dihitung secara dinamis atau diperbarui melalui model event/observer berdasarkan perbandingan **Saldo Item** dengan `target_amount`:

*   Jika $\text{Saldo Item} \ge \text{target\_amount} \rightarrow$ `ItemStatus::Terpenuhi`
*   Jika $\text{Saldo Item} > 0 \text{ DAN } < \text{target\_amount} \rightarrow$ `ItemStatus::Proses`
*   Jika $\text{Saldo Item} \le 0 \rightarrow$ `ItemStatus::Belum`

### C. Real Account Balancing (Dompet/Tempat Simpan)
Formula saldo riil pada tempat penyimpanan (`Account`):

$$\text{Saldo Account} = \text{initial\_balance} + \sum \text{Income} + \sum \text{Transfer In} - \sum \text{Expense} - \sum \text{Transfer Out}$$

*   **Transfer In:** Transaksi berjenis `transfer` di mana `target_account_id` adalah ID account ini.
*   **Transfer Out:** Transaksi berjenis `transfer` di mana `account_id` (asal) adalah ID account ini.

---

## 2. Automatic Field & Date Extraction

Setiap kali transaksi disimpan (`creating` / `updating`), field `month` dan `year` **wajib diekstrak secara otomatis** dari `transaction_date`:

*   `month`: Angka bulan `1` hingga `12` (`(int) $date->format('n')`).
*   `year`: Angka tahun 4 digit e.g., `2026` (`(int) $date->format('Y')`).

---

## 3. Data Integrity & Enum Rules

### Enums Location & Structure
Semua Enum harus ditempatkan di namespace `App\Enums` dan menggunakan Backed Enums (string/int) dengan `TitleCase` pada setiap keys:

*   **`TransactionType`** (`app/Enums/TransactionType.php`):
    *   `Income = 'income'`
    *   `Expense = 'expense'`
    *   `Transfer = 'transfer'`
*   **`ItemPriority`** (`app/Enums/ItemPriority.php`):
    *   `Wajib = 'wajib'`
    *   `RutinBulanan = 'rutin_bulanan'`
    *   `KeinginanShortterm = 'keinginan_shortterm'`
    *   `Emergency = 'emergency'`
*   **`ItemStatus`** (`app/Enums/ItemStatus.php`):
    *   `Belum = 'belum'`
    *   `Proses = 'proses'`
    *   `Terpenuhi = 'terpenuhi'`
*   **`AccountType`** (`app/Enums/AccountType.php`):
    *   `Cash = 'cash'`
    *   `Bank = 'bank'`
    *   `EWallet = 'ewallet'`
    *   `Savings = 'savings'`

---

## 4. Input Formatting & Sanitization

*   **Currency Inputs:** Nominal angka yang diinput dari form frontend (misalnya `Rp 150.000` atau `150,000`) harus selalu dibersihkan sebelum disimpan ke database menjadi format `decimal(15, 2)` murni.
*   **Cascading Options:** Pada form pencatatan transaksi (`Fast-Entry`), dropdown `Item` wajib disaring secara dinamis (*dependent dropdown*) sesuai dengan `category_id` yang dipilih user.

---

## 5. Architectural & Code Conventions

1.  **Strict Typing:** Selalu gunakan type hints pada argumen dan return types di semua method.
2.  **Model Observers/Events:** Pembaruan `status` pada `Item` secara otomatis dipicu via `TransactionObserver` (event `saved`, `updated`, `deleted`) menggunakan dedicated service `App\Services\FinancialCalculatorService`.
3.  **Scope Queries:** Sertakan query scope pada `Transaction` untuk mempermudah filtering dashboard:
    *   `scopeForMonthYear($query, int $month, int $year)`
    *   `scopeForUser($query, int $userId)`