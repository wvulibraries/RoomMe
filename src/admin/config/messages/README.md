Messages
========

These are, mostly, results messages displayed for:

* Success
* Warnings
* Errors

The messages *reservationCreated* and *maxFineExceeded* can contain variables.

reservationCreated can contain *{roomName}* to display the display name of the 
room

maxFineExceeded can contain *{amount}* which will be replaced by the maximum fine
defined in the policy for the room.

roomClosed is the default message displayed if a room is closed by a policy or individually, and a snippet is not defined in the rooms policy.