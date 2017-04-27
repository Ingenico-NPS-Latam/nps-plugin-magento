# nps-plugin-magento

## Pasos para la instlacion
1. Clonar el proyecto.
2. Copiar el contenido del plugin en el raiz de instalacion de Magento  
[Folder](https://developers.nps.com.ar/images/devicon/nodejs-original.svg)   
[Folder](https://developers.nps.com.ar/images/screenshot_plugins/magento_1.7_1.9/1.png)  
3. Erase all cache  
  system ==> Cache Management  
[Folder](https://developers.nps.com.ar/images/screenshot_plugins/magento_1.7_1.9/2.png)  
[Folder](https://developers.nps.com.ar/images/screenshot_plugins/magento_1.7_1.9/3.png)  
  ==> Flush Magento Cache  
  ==> Flush Cache Storage    
  ==> Flush Catalog and Images Cache  
[Folder](https://developers.nps.com.ar/images/screenshot_plugins/magento_1.7_1.9/4.png)  
  ==> Flush Javascript/css Cache  

*Con esto se agrega la Opcion NPS al menu de Magento  

4. Configure your new NPS payment method ==> System ==> Configuration ==>  Payment Methods:  
Completar con la informacion del Comercio:  
[Folder](https://developers.nps.com.ar/images/screenshot_plugins/magento_1.7_1.9/5.png)  
Enable ==> Yes   ||  No  
New order status  ==> Processing  
Payment Action ==> Autorize  || Authorize and Capture  
Title  ==> NPS (Net Payment Service)  
Secret Key  ==> Secret Key Asignado al comercio  
Gateway URL ==> https://implementacion.nps.com.ar/ws.php?wsdl  
Merchant ID  ==>  Merchant_id Asignado al comercio  
Merchant's Email ==> mail@mail.com  
Credit Card Types ==> Elegir las Tarjetas con las que se quiere operar  
Payment from Applicable Countries ==> All Allowed Countries  || Specific Countries  
Payment from Specific Countries ==> Elegir los paises en los que se quiere operar  

5. Configure installment settings to allow payments ==> Clicke en NPS del Menu Principal ==> Installments ==> Add Installments  
[Folder](https://developers.nps.com.ar/images/screenshot_plugins/magento_1.7_1.9/6.png)  
cc_type ==> Marca de la Tarjeta  
qty ==> Cantidad de Cuotas  
rate ==> Interes  
Status ==> Enable   ||   Disable  
Country ==> AR  
Currency ==> ARS  
[Folder](https://developers.nps.com.ar/images/screenshot_plugins/magento_1.7_1.9/7.png)  

## Consideraciones Finales
Si no se realizo durante la instalacion, hay que verificar / Configurar las opciones de Pais y Moneda  
  System ==> Configuration ==> General  
[Folder](https://developers.nps.com.ar/images/screenshot_plugins/magento_1.7_1.9/8.png)  
Configurar las opciones de Pais  
  System ==> Configuration ==> Currency Setup  
Configurar las opciones de moneda dentro de la ocpion Currency Options  
[Folder](https://developers.nps.com.ar/images/screenshot_plugins/magento_1.7_1.9/9.png)  
[Folder](https://developers.nps.com.ar/images/screenshot_plugins/magento_1.7_1.9/10.png)  




