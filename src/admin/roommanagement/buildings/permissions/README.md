**NOTE: All code in this branch is untested (i haven't even checked for correct syntax). It should be considered psuedo or outline code.**

Permissions will be username based.

Should take a file to upload, or be done via cron. A simple class should be able to easily handle each use case.

The file should be 1 record per line, and each line should be the username of the person that needs permission.

Permissions should be building based.

1. We need a new mysql
  * ID
  * username
  * resourceType
  * resourceID

We are doing "resourceType" and resourceID in case we need to expand this to "Room", "Template", or "Policy" at a later date. the plumbing will all be in place.

If the buildingID has a count > 0 in the permissions table where resourceType == building and resourceID=buildingID then the room shouldn't allow the user to create a reservation without the username being present in the permissions table.


We need an interface to view all the permissions

I don't think we should allow editing permissions via this interface. All updates should come from the file. When updating, the file is expected to be a complete list of everyone that is required.

More specific to least specific permissions. More specific permissions will override least specific.

1. building
1. Policy
1. Template
1. room

So, if room has a permission THOSE users will be checked and all other permissions in the chain will be ignored.
If room has no permissions, Template permissions will be used (if they exist) and so on.

On a first implementation on "Building" permissions are being built out. But the back end class should be prepared to except additional needs in the future.

Constants are provided for resource types. see vars.php
