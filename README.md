# **Anonymize Comment IPs**  

**Anonymize Comment IPs** is a **lightweight and GDPR-compliant** WordPress plugin that **automatically anonymizes** comment IP addresses and provides a tool for **bulk anonymization of existing IPs**.

---

## **Disclaimer**  

This plugin is provided **"as is"** without warranties or guarantees of any kind. The author disclaims all implied warranties, including but not limited to merchantability, fitness for a particular purpose, and non-infringement of third-party rights. **Use of this plugin is at the user's own risk.**

---

## **Description**  

WordPress **stores commenters' IP addresses** by default, which can raise **privacy concerns** and **GDPR compliance issues**.  
This plugin ensures compliance by:  

✔ **Automatically anonymizing all new comment IPs** before they are stored.  
✔ **Providing an admin tool** to **bulk anonymize** existing comment IPs in the database.  
✔ **Supporting IPv4 & IPv6 anonymization** for maximum compatibility.  

---

## **Installation**  

1. **Upload & Install**  
   - Upload the plugin folder to `/wp-content/plugins/anonymize-comment-ip/`,  
   - Or install it directly via the **WordPress Plugin Repository**.  

2. **Activate the Plugin**  
   - Go to **Plugins → Installed Plugins** and activate **Anonymize Comment IPs**.  

3. **Automatic Anonymization Starts Immediately**  
   - All **new comments** will have their IPs anonymized automatically.  
   - To anonymize **existing IPs**, use the **admin tool** (see below).  

---

## **Usage**  

### **Automatic Anonymization**  
- The plugin **automatically anonymizes** all **new** comment IPs **before** they are saved to the database.  

### **Bulk Anonymization of Existing IPs**  
1. Go to **WordPress Dashboard → Anonymize IPs**.  
2. Click the **"Start Anonymization"** button to anonymize **all previously stored** comment IPs.  
3. A confirmation message will display the **number of anonymized IPs**.  

---

## **Features**  

✔ **Automatic GDPR-Compliant IP Anonymization** – No user intervention required.  
✔ **Bulk Anonymization** – One-click anonymization of all stored comment IPs.  
✔ **Supports IPv4 & IPv6** – Ensures full compatibility.  
✔ **Optimized Database Performance** – Only updates necessary records.  
✔ **Admin-Friendly UI** – Easily manage everything in the WordPress dashboard.  
✔ **Lightweight & Efficient** – Minimal performance impact.  

---

## **Function Reference**  

### **`pixovoid_anonymize_ip($ip)`**  
Anonymizes an IP address (supports both IPv4 & IPv6).  

- **Parameter:**  
  - `$ip` *(string)* – The original IP address.  
- **Returns:**  
  - *(string)* – The anonymized IP address.  

**Example usage:**  
```php
$ip = '192.168.1.45';
$anonymized_ip = pixovoid_anonymize_ip($ip); 
// Output: 192.168.1.0
```

---

### **`pixovoid_filter_comment_ip($comment_ip)`**  
Filters and anonymizes IPs for **new** comments **before saving** them.  

- **Parameter:**  
  - `$comment_ip` *(string)* – The original comment IP.  
- **Returns:**  
  - *(string)* – Anonymized IP.  

---

### **`pixovoid_anonymize_existing_ips()`**  
Anonymizes **all previously stored** comment IPs in the database.  

- **Returns:**  
  - *(int)* – The number of updated comments.  

---

## **License**  

This plugin is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for more information.  

---

## **Author**  

Developed by **[PixoVoid.net](https://pixovoid.net/)**.  

---

### 🚀 **Ensure GDPR compliance effortlessly with Anonymize Comment IPs!** 🚀  