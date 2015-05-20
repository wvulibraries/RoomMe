## WVU Libraries' Room Reservations

Room Reservations provides a robust, secure, and highly configurable room reservation software platform that can handle the requirements multiple buildings, rooms, and policies with a single installation. 

## Requirements:

Room Reservations has only been tested on Linux, running Apache, PHP, and MySQL. Room Reservations uses the EngineAPI Framework, version 4. 

## Testing:

We have provided a vagrant setup to test Room Reservations locally. [Install Vagrant](https://www.vagrantup.com/), change to the directory where you cloned this repository and type "vagrant up" on your command line.

To view the system as an unauthenticated patron, go to [localhost:8090/services/rooms/](http://localhost:8090/services/rooms/).

To authenticate, we have provided a script that will authenticate you to the local vagrant box, please visit [Vagrant Login](http://localhost:8090/services/rooms/vagrantLogin.php). Once authenticated visit the [Admin Interface](http://localhost:8090/services/rooms/admin)

By default no data is loaded into the database. You will need to create buildings, rooms, templates, and configure the system. A SQL file, roomReservations.sql is provided as a way to test the system with some default data. This is a stripped down copy of the production database at WVU Libraries. To load this file by default uncomment the last line in the bootstrap.sh file. The example dataset includes 3 buildings, 2 that use the RoomMe software and one (Health Sciences Library) that links to an external form. 

## Setup:

1. Setup [EngineAPI 4.0](https://github.com/wvulibraries/Engineapi/) on your server
	* Upload the files outside of your document root
	* %%%More Steps Here%%%
1. Upload RoomMe files to the server
1. Delete the vagrantLogin.php file
1. modify the roomReservationHome localvar, in src/includes/vars.php to reflect the path to the software
1. %%%User Information setup here%%%
1. %%%Database Connection Setup information here%%%
1. Configure your software

### Configuration:

1. [System](https://github.com/wvulibraries/RoomMe/blob/develop/src/admin/config/settings/README.md)
1. [Messages](https://github.com/wvulibraries/RoomMe/blob/develop/src/admin/config/messages/README.md)
1. [Via](https://github.com/wvulibraries/RoomMe/blob/develop/src/admin/config/via/README.md)
1. [Statistics](https://github.com/wvulibraries/RoomMe/blob/develop/src/admin/config/statistics/README.md)
1. [Buildings](https://github.com/wvulibraries/RoomMe/blob/develop/src/admin/roommanagement/buildings/README.md)
1. [Policies](https://github.com/wvulibraries/RoomMe/blob/develop/src/admin/roommanagement/policies/README.md)
1. [Templates](https://github.com/wvulibraries/RoomMe/blob/develop/src/admin/roommanagement/templates/README.md)
1. [Rooms](https://github.com/wvulibraries/RoomMe/blob/develop/src/admin/roommanagement/rooms/README.md)
1. [Snippets](https://github.com/wvulibraries/RoomMe/blob/develop/src/admin/config/snippets/README.md)

### Hours Information

RoomMe allows for an Hours RSS URL per building. When defined this will prevent RoomMe from allowing reservations while a building is closed. 



### Fines Information

### Access Control

Access Controls in Room Reservations are controlled via EngineAPI's Access Controls. By default they use Active Directory Security Groups. 

1. Public Interface
	* By default there is no access control in the document root of the sofware. This can be changed by adding a acl.php file to the root of the room reservation software.
	* To make a reservation the users must be logged into the software, unauthenticated reservations are not permitted. 

1. Admin
	* libraryWeb_roomReservation - Can access the admin interface and there functions:
		* Create/Edit reservations
		* Create/Edit series reservations
		* List all (and delete) reservations
		* Print a reservations page
		* Search for a users reservations. 
	* libraryWeb_roomReservation_rooms - Can manage rooms
		* Create/Edit new buildings
		* Create/Edit new room Policies
		* Create/Edit new Room Templates
		* Create/Edit new Equipment & Equipement Types
		* Create/Edit new Rooms
	* libraryWeb_roomReservation_admin 
		* Manage system messages
		* Manage via messages
		* Manage system wide default settings
		* View Statistics
		* Create/Edit snippets

All of the above functions can have individual ACLs added to their respective directories to further refine access controls, either using security groups or individual user accounts. 