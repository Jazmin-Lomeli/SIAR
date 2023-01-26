/* Librerias Necesarias */

#include <Adafruit_Fingerprint.h>  // Lector de huella 
#include <HTTPClient.h>            // Conexion con el servidor
#include <WiFi.h>                  // Conexion a internet
#include <WebServer.h>             // Conexion al servidor

/* Credenciales para el WIFI */
#define WIFI_SSID "INFINITUM494B_2.4"  // nombre de la red 2.4 ghz
#define WIFI_PASSWORD "CH4x47bJeX"     // contraseña de la red

int LED = 2;                          // Led para encender y apagar 

String url = "http://192.168.1.74/modular/arduino_resp.php";    //  Direccion del servidor local 
/*  La IP cambia cada cierto tiempo segun el DHPC*/

/*  Variables para cambiar el status y recibir la informacion del servidor */
String finger_err = "-";           
String transaccion_hecha ="-";
String ID = "-";
String error = "-";
String aux = "-";

/*  Creamos objeto para la libreria del sensor de huella */ 
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&Serial2);
uint8_t id;

void setup() {
  //Inicializamos la cosola y el lector de huella 
  Serial.begin(57600);
  finger.begin(57600);
  // Modo del LED 
  pinMode(LED, OUTPUT);
  // Inicializamos conexion a wifi con las credencailes 
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  Serial.print("conectando..");
   
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  // Logro conectarse   
  Serial.println();
  Serial.print("connected: ");
  Serial.println(WiFi.localIP());    // Mostramos IP

/* Encontro el lector de huella */
  if (finger.verifyPassword()) {
    Serial.println("Found fingerprint sensor!");
    finger_err = "Todo_bien";             // cambiamos status
  } else {
    Serial.println("Did not find fingerprint sensor :(");
    while (1) { delay(1); }
  }

}

/* Ciclo */ 
void loop() {
 // Mientras este conectado a Wifi
  while (WiFi.status() == WL_CONNECTED) {

    HTTPClient http;       // Inicializamos cleinte HTTP
    
/* LO que se va a enviar es:
    finger_err        --> Si el lector de huella si esta funcionando 
    transaccion_hecha --> que transaccion se hizo
    ID                --> ID que se manipulo  
    error             -->  confirmar transacción
*/

    String postData = "finger_err="+ finger_err + "&transaccion_hecha="+ transaccion_hecha + "&id=" +ID +"&error=" +error; 
    
      http.begin(url);                       // Mandamos peticion a servidor con la URL ya establecida arriba 
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");   // Necesario para enviar los datos por POST 
// Mostramos lo que envia      
      Serial.println("\n ENVIADO   ");
      Serial.println(postData);
      Serial.println("\n");
          
    int httpCode = http.POST(postData);            // El codigo que envia el server 
    delay(1000);   
/* Limpiamos variables para que no guarden basura */
    error = "-";
    transaccion_hecha = "-";
    ID = "-";      
/* LO que se va a recibir es el modo del sensor y el ID del huella a registrar o borrar, si no es nada de eso mandamos un - */ 
    String respuesta = http.getString();
    Serial.print("Codigo de respuesta: ");
    Serial.println(httpCode);              // El servidor respondio 
    Serial.print("El server respondio: ");
    Serial.println(respuesta);  
    
// Lo que rebibe es REGISTER/-, debemos descomponer el string para establecer los datos 
    int index = respuesta.indexOf('/');                        // contar espacios hasta el /
    int length = respuesta.length();                  
    String accion_sensor = respuesta.substring(2,index);       // EXTRAERMOS LA ACCION A REALIZAR
    ID = respuesta.substring(index+1, length);                 // Extraemos el ID o el -

/* Si esta en modo register quiere decir que esta esperando que llegue alguien */ 
    if(ID == "-" && accion_sensor == "REGISTER"){
            
      Serial.print("ESTAMOS EN ESPERAR PARA REGISTRAR ENTRADA O SALIDA \n");

      getFingerprintID();                   // Llamamos al metodo de esperar huella 
      ID =  finger.fingerID;                // Si detecta una huella la guardamos en ID
      
      if(aux !="-"){                        // Mientras no encuentre nuinguna huella 
          Serial.print("\n DATOS NO VALIDOS PARA ASISTENCIA \n");
          ID = "-";
      } else{                               // Ha detectado una huella registrada
        transaccion_hecha = "register";     /* Actualizamos variables */ 
        error = "correcto";
/* Encedemos el LED para que el usuario se de cuenta que ya puede retirar el dedo */ 
        encender_led();
      } 
    }

/* 
    Si esta en modo DELETE quiere decir que se va a borrar una huella
    AÚN NO ESTA EMPLEMENTADO
*/ 
   if(accion_sensor == "DELETE"){
    id = readnumber();
     Serial.print("ESTAMOS EN BORRAR \n");
     Serial.print(id);
            
   }
  
/* Si esta en modo ENROLL quiere decir que se va a agregar una nueva huella   */
    
   else if(accion_sensor == "ENROLL"){  
      id = readnumber();           // ID para guardar la huella
      
      Serial.print("ESTAMOS EN ENROLAR \n");
      Serial.print(id);

    while(transaccion_hecha != "add"){    /* Hasta que la huella se agregue correctamente */
        getFingerprintEnroll();           // Llamamos a metodo 
    }

    ID =  String(id);     /* Converimos id a string para enviarlo al server y posteriormnete  a la bd */ 
    error = "correcto";
    encender_led();        // Encendemos el LED para que el usuario se de cuenta que se registro su huella    
    delay(500);  
   } 

    http.end();
    delay(5000);  /* Repetimos cada 5 segundos */ 
  }
}

/*  
  Metodo para convertir el String del ID en el tipo de dato unit8_t
*/
uint8_t readnumber(void) {
  uint8_t num = 0;
    num = ID.toInt();
  return num;
}

/* 
  Método ENROLL 
  Agrega una nueva huella, 
  El método lo facilita la libreria <Adafruit_Fingerprint.h>
*/ 
uint8_t getFingerprintEnroll() {
  int p = -1;
  Serial.print("Waiting for valid finger to enroll as #"); Serial.println(id);
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image taken");
      break;
    case FINGERPRINT_NOFINGER:
      Serial.println(".");
      break;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      break;
    case FINGERPRINT_IMAGEFAIL:
      Serial.println("Imaging error");
      break;
    default:
      Serial.println("Unknown error");
      break;
    }
  }
  // OK success!
  p = finger.image2Tz(1);
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  Serial.println("Remove finger");
  encender_led();                                          // Encender LED para indicarle al usuario que levante su dedo
   delay(2000);
  p = 0;
  while (p != FINGERPRINT_NOFINGER) {
    p = finger.getImage();
  }
  Serial.print("ID "); Serial.println(id);
  p = -1;
  Serial.println("Place same finger again");
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image taken");
      break;
    case FINGERPRINT_NOFINGER:
      Serial.print(".");
      break;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      break;
    case FINGERPRINT_IMAGEFAIL:
      Serial.println("Imaging error");
      break;
    default:
      Serial.println("Unknown error");
      break;
    }
  }

  // OK success!

  p = finger.image2Tz(2);
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  // OK converted!
  Serial.print("Creating model for #");  Serial.println(id);

  p = finger.createModel();
  if (p == FINGERPRINT_OK) {
    Serial.println("Prints matched!");
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_ENROLLMISMATCH) {
    Serial.println("Fingerprints did not match");
    return p;
  } else {
    Serial.println("Unknown error");
    return p;
  }

  Serial.print("ID "); Serial.println(id);
  p = finger.storeModel(id);
  if (p == FINGERPRINT_OK) {
    Serial.println("Stored!");         
   transaccion_hecha = "add";                             // Si se guardo la huella correctamente 
  
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_BADLOCATION) {
    Serial.println("Could not store in that location");
    return p;
  } else if (p == FINGERPRINT_FLASHERR) {
    Serial.println("Error writing to flash");
    return p;
  } else {
    Serial.println("Error writing to flash");
    return p;
  }
}

/* 
  Método para esperar a que llegue una huella 
  El método lo facilita la libreria <Adafruit_Fingerprint.h>
*/ 
uint8_t getFingerprintID() {
  uint8_t p = finger.getImage();
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image taken");
       aux = "-";
      break;
    case FINGERPRINT_NOFINGER:
      Serial.println("No finger detected");
      aux = "No finger detected"; 
      transaccion_hecha = "-";
 
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_IMAGEFAIL:
      Serial.println("Imaging error");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  // OK success!

  p = finger.image2Tz();
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  // OK converted!
  p = finger.fingerSearch();
  if (p == FINGERPRINT_OK) {
    Serial.println("Found a print match!");
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_NOTFOUND) {
    Serial.println("Did not find a match");
    return p;
  } else {
    Serial.println("Unknown error");
    return p;
  }

  // found a match!
  Serial.print("Found ID #"); Serial.print(finger.fingerID);
  Serial.print(" with confidence of "); Serial.println(finger.confidence);

  return finger.fingerID;
}

// returns -1 if failed, otherwise returns ID #
int getFingerprintIDez() {
  uint8_t p = finger.getImage();
  if (p != FINGERPRINT_OK)  return -1;

  p = finger.image2Tz();
  if (p != FINGERPRINT_OK)  return -1;

  p = finger.fingerFastSearch();
  if (p != FINGERPRINT_OK)  return -1;

  // found a match!
  Serial.print("Found ID #"); Serial.print(finger.fingerID);                             // Encontro una heulla que esta registrada
  Serial.print(" with confidence of "); Serial.println(finger.confidence);
  return finger.fingerID;  
  transaccion_hecha = "-";                                                               // Si se guardo 
}

/*
  Método para encender un LED para indicarle al usuario que si se detecto su huella 
*/
void encender_led(){
  digitalWrite(LED, HIGH);
  delay(2500);
  digitalWrite(LED, LOW);
  delay(500); 
  digitalWrite(LED, HIGH);
  delay(1000);
  digitalWrite(LED, LOW);
  delay(500); 
}

 