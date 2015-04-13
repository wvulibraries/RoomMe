## WVU Libraries' Room Reservations

Room Reservations provides a robust, secure, and highly configurable room reservation software platform that can handle the requirements multiple buildings, rooms, and policies with a single installation. 

## Requirements:

Room Reservations has only been tested on Linux, running Apache, PHP, and MySQL. Room Reservations uses the EngineAPI Framework, version 4. 

## Testing:

We have provided a vagrant setup to test Room Reservations locally. [Install Vagrant](https://www.vagrantup.com/), change to the directory where you cloned this repository and type "vagrant up" on your command line.

To view the system as an unauthenticated patron, go to [localhost:8090/services/rooms/](http://localhost:8090/services/rooms/).

To authenticate, we have provided a script that will authenticate you to the local vagrant box, please visit [Vagrant Login](http://localhost:8090/services/rooms/vagrantLogin.php). Once authenticated visit the [Admin Interface](http://localhost:8090/services/rooms/admin)

## Setup:

1. Upload files to the server
1. Delete the vagrantLogin.php file
1. modify the roomReservationHome localvar to reflect the path to the software
1. %%%User Information setup here%%%
1. %%%Database Connection Setup information here%%%
1. Configure your software

### Configuration:

1. [System](https://github.lib.wvu.edu/wvulibraries/Room-Reservations/tree/documentation/src/admin/config/settings/README.md)
1. [Messages](https://github.lib.wvu.edu/wvulibraries/Room-Reservations/tree/documentation/src/admin/config/messages/README.md)
1. [Via](https://github.lib.wvu.edu/wvulibraries/Room-Reservations/tree/documentation/src/admin/config/via/README.md)
1. [Statistics](https://github.lib.wvu.edu/wvulibraries/Room-Reservations/tree/documentation/src/admin/config/statistics/README.md)
1. [Buildings](https://github.lib.wvu.edu/wvulibraries/Room-Reservations/tree/documentation/src/admin/roommanagement/buildings/README.md)
1. [Policies](https://github.lib.wvu.edu/wvulibraries/Room-Reservations/tree/documentation/src/admin/roommanagement/policies/README.md)
1. [Templates](https://github.lib.wvu.edu/wvulibraries/Room-Reservations/tree/documentation/src/admin/roommanagement/templates/README.md)
1. [Rooms](https://github.lib.wvu.edu/wvulibraries/Room-Reservations/tree/documentation/src/admin/roommanagement/rooms/README.md)
1. [Snippets](https://github.lib.wvu.edu/wvulibraries/Room-Reservations/tree/documentation/src/admin/config/snippets/README.md)

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