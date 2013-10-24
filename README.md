Bitwiser
========

**Bitwiser** is a utility class to help managing bitwise flags!

Bitwise flags are a convenient way to store multiple true/false values (flags) in a single integer based database column. The number of bitwise flags are *however* limited to the maximum value of an integer of the system (which is 64 on a 64bit system or 32 on a 32bit system).

Example usage
-------------

Create a class that extends `AbstractBitwiser` and declare the flags as class constants.

```
:::php
class PermissionsBitwiser extends AbstractBitwiser
{
    const CAN_EDIT_POSTS = 0;
    const CAN_DELETE_POSTS = 1;
    const CAN_CREATE_USERS = 2;
}
```

Initialize the class with a starting state and callback

```
:::php
$state = 0; // this value is passed by reference

$permissions = new PermissionsBitwiser($state, function (AbstractBitwiser $bitwiser) {
	echo $bitwiser->getState();
});

$permissions->add(PermissionsBitwiser::CAN_EDIT_POSTS); // echoes 1
$permissions->add(PermissionsBitwiser::CAN_DELETE_POSTS); // echoes 3
$permissions->add(PermissionsBitwiser::CAN_CREATE_USERS); // echoes 7
$permissions->remove(PermissionsBitwiser::CAN_DELETE_POSTS); // echoes 5

$permissions->getState(); // int(5)
$permissions->has(PermissionsBitwiser::CAN_EDIT_POSTS); // true
$permissions->has(PermissionsBitwiser::CAN_DELETE_POSTS); // false

```

Example usage with ORM (eg. Laravel Eloquent) for persisitence 
--------------
The end goal is to persist the integer value to a database column while maintaining a clean OO method of updating the value.

```
:::php
class User extends Model
{
    public function getPermissionsAttribute()
    {
        $state = $this->attributes['permissions']; // Don't pass this by reference
        $self = $this;
        return new PermissionsBitwiser($state, function ($bitwiser) use ($self) {
            $self->permissions = $bitwiser->getState();
        });
    }
}

$user = new User;

$user->permissions->add(PermissionsBitwiser::CAN_CREATE_USERS);

$user->save();

$user->permissions->has(PermissionsBitwiser::CAN_DELETE_POSTS); // false
$user->permissions->has(PermissionsBitwiser::CAN_CREATE_USERS); // true. etc


```


