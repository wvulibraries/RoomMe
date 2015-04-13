Policy Settings
===============

Policies define behavior for specific templates. These can be used to override 
policies on buildings and system wide settings.

**Policy Name**  
: Name to identify the policy

**Description**  
: A note so that future maintainers of the system know what a specific policy was
supposed to be used for.

**URL**  
: A web page URL describing the policy for the public. This can be an external webpage or a snippet.

**Period**  
: Number of days in a "period."  The period is a a running period based on the current time. If a period of 14 is set the current period is 7 days prior and 7 days ahead of the current date. 

**Hours per Period**  
: Maximum number of hours that a patron can schedule in a given period. 

**Bookings per Period**  
: Maximum number of reservations that a patron con contain in a given period.

**Max Loan Length**  
: The maximum number of hours that the room can be reserved by a patron.

**Fine Amount**  
: Maximum fine allowed for booking a room in this building, over rides the system setting.

**Public Scheduling**  
: Can the rooms in this policy be scheduled via the public interface, or do they need to be scheduled via the staff interface?

**Public Viewing**  
: Is the room visible on the public interface. Useful for hiding a room that is not currently available or has been removed. If we hide the room, instead of deleting it, then it will still show up in statistics. 

**Create Same Day Reservation**  
: Allow the users to create reservations for the current day. If set to know only future reservations are allowed.

**Reservations Increments**  
: The increments, in minutes, that a room can be reserved for. Default: 15 minutes

**Future Schedule Length**  
: How many days into the future can this room be scheduled. **NOTE:** if the hours calendar (defined in the building setup) hasn't been populated this far out, errors or inconsistences may be encountered. 