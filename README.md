# Magento Plugin

*Read this in other languages: [English](README.md), [Español](README.es.md)

## Availability
Supported & Tested in Magento versions 1.7.x.x to 1.9.x.x

## Installation steps
1. Clone the project.
2. Copy the plugin content into the home directory of Magento

  ![1](https://cloud.githubusercontent.com/assets/24914148/25488577/c34daeda-2b3d-11e7-8c21-ba08d45ba890.png)

3. Erase all cache  
  system ==> Cache Management  

  ![2_cache_mangement](https://cloud.githubusercontent.com/assets/24914148/25488624/df814fb2-2b3d-11e7-86be-8e644490fe51.png)

  ![3](https://cloud.githubusercontent.com/assets/24914148/25488626/df84dfce-2b3d-11e7-8779-7ee35da1d904.png)

  ==> Flush Magento Cache  
  ==> Flush Cache Storage    
  ==> Flush Catalog and Images Cache  

  ![4](https://cloud.githubusercontent.com/assets/24914148/25488625/df848b1e-2b3d-11e7-85c0-86e7b25f780f.png)

  ==> Flush Javascript/css Cache  

  *By doing this, NPS option is added to Magento menu

4. Configure your new NPS payment method ==> System ==> Configuration ==>  Payment Methods:  
  Complete with Merchant data:  

  ![5](https://cloud.githubusercontent.com/assets/24914148/25488627/df84ef82-2b3d-11e7-94e8-e35a4be558f2.png)  

  Enable ==> Yes   ||  No  
  New order status  ==> Processing  
  Payment Action ==> Autorize  || Authorize and Capture  
  Title  ==> NPS (Net Payment Service)  
  Secret Key  ==> Secret Key Assigned to Merchant
  Gateway URL ==> https://implementacion.nps.com.ar/ws.php?wsdl  
  Merchant ID  ==>  Merchant_id Assigned to Merchant
  Merchant's Email ==> mail@mail.com  
  Credit Card Types ==> Choose cards brand to use
  Payment from Applicable Countries ==> All Allowed Countries  || Specific Countries  
  Payment from Specific Countries ==> Choose countries to use

5. Configure installment settings to allow payments ==> Click in NPS from Main Menu ==> Installments ==> Add Installments  

  ![6](https://cloud.githubusercontent.com/assets/24914148/25488628/df89e1fe-2b3d-11e7-8613-f1ad2486e7b5.png)  

  cc_type ==> Card Brand   
  qty ==> Installment quantities  
  rate ==> Interst  
  Status ==> Enable   ||   Disable  
  Country ==> AR  
  Currency ==> ARS  

  ![7](https://cloud.githubusercontent.com/assets/24914148/25488629/dfceaa0a-2b3d-11e7-888e-ffc6130891dc.png)

## Last but not least
If you did not do it during the installation, Verify (or Configure) Currency and Country  
System ==> Configuration ==> General  
![8](https://cloud.githubusercontent.com/assets/24914148/25488630/dfe32c64-2b3d-11e7-9132-23dcf6b1ed2d.png)

Configure Country options   
System ==> Configuration ==> Currency Setup  

Configure currency options    
![9](https://cloud.githubusercontent.com/assets/24914148/25488631/dfecbc02-2b3d-11e7-9480-6b28a992910d.png)
![10](https://cloud.githubusercontent.com/assets/24914148/25488632/e0400ab0-2b3d-11e7-8a80-74feba06f70e.png)  
