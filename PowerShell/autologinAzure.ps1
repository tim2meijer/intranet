## Code gevonden op
# http://www.mirazon.com/automating-office-365-login-in-powershell/
# http://dennisspan.com/powershell-function-library/

## Automatisch inloggen
$TenantUserName = "matthijs.draijer@koningskerkdeventer.nl"
$TenantPassword = cat "D:\xampp\htdocs\3GK\intranet\PowerShell\koningskerkdeventer.key" | ConvertTo-SecureString
$TenantCredentials = new-object -typename System.Management.Automation.PSCredential -argumentlist $TenantUserName, $TenantPassword
Connect-AzureAD -Credential $TenantCredentials

# Script regels
