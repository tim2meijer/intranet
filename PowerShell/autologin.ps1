# Code gevonden op
# http://www.mirazon.com/automating-office-365-login-in-powershell/

#########################################################################
###         AutoLogin Script                                          ###
#########################################################################
#Start-transcript
Import-Module MSOnline
$TenantUserName = "matthijs.draijer@koningskerkdeventer.nl"



#########################################################################
### This next line is password is the key you created earlier         ###
### Remember you need the full path to it.                            ###
#########################################################################
$TenantPassword = cat "D:\xampp\htdocs\3GK\intranet\PowerShell\koningskerkdeventer.key" | ConvertTo-SecureString
$TenantCredentials = new-object -typename System.Management.Automation.PSCredential -argumentlist $TenantUserName, $TenantPassword



##########################################################################
###	Connect your Session to Office 365	                       ###
##########################################################################
Connect-MsolService -Credential $TenantCredentials



##########################################################################
#### This last part Grabs some info out of the Tenant                 ####
#### and places it on the screen and title bar                        ####
##########################################################################
$Company = Get-MsolCompanyInformation | select -exp DisplayName
$InitialDomain = Get-MsolCompanyInformation | select -exp InitialDomain
$host.ui.RawUI.WindowTitle = "You are connected to: " + $Company + " (" + $InitialDomain + ") "