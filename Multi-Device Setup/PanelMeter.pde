#include <Ethernet.h>

//----- Configuration ------
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
byte ip[] = { 10, 0, 1, 177 };  //change for different local networks
byte server[] = { 0, 0, 0, 0 }; 
Client client(server, 80);

const int numOfDevices = 2; //must change with arrays below:
int value[] = {0, 0}; //value to display on meter for each meter/device
const int device[] = {1, 2};  //device identifiers (on server)
const int meterPin[] = {5, 6}; //pins to associate with each device

unsigned long secondsDelay = 60;  //polling interval

void setup() {
 Serial.begin(9600);
 Ethernet.begin(mac, ip);
 delay(1000);
}

void loop() {
for (int i = 0; i < numOfDevices; i++) //iterate for each device
    {
     if (client.connect()) {
        client.print("GET /path/on/server");
        client.println(device[i]); //Send URL parameter for device number
        client.println();
        delay(1000);
     }
     else
       Serial.println("Connection failed.");
    do {
     if (client.available()) {
      Serial.println("Reading data from server...");
      value[i] = client.read();
      client.flush();
    }
    } while ( client.connected() );
    if (!client.connected()) {
      client.stop();
    } 
     analogWrite(meterPin[i], value[i]);
     Serial.println(value[i]); 
  } //end for each device
  
   delay(secondsDelay * 1000);
}
