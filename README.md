# silex-simple-annotations

## When is the last night you dreamed of annotations for every controller route ?
Silex-simple-annotations allows you to get ride of controller providers and have it done your(our) way.

Prepand your controller function with annotations, we will deal with the rest. 

```php

    /**
     * @Route /login 
     * @Method POST
     * @Bind user.login
     */
    public function loginRegisterAction(Application $app, Request $request)
    {
        $user = SomeSuperStaticClass::loginOrRegisterThisGuy($request, $app);
        return $app->json();
    }
```

This examples links a `POST` on `baseUrl/SomeControllerPrefixRouteYouGave/login` to the `loginRegisterAction` action

## What can i do ? Is this FREE ? 

### Registering
You just have to [...]

//TODO : Register sur packages - composer

When registering services into your app, add this one 

// TODO : Changer le path post new 

```php
$app->register(new OriginalNamespace\AnnotationsServiceProvider(), array(
    'simpleAnnots.controllersPath' => __DIR__ . '/../src/Controller',
    'simpleAnnots.recursiv' => true, // Optional, default to false
));
```
Parameters start with **simpleAnnots** and end with:
* **controllersPath** : String or Array of controller directory/ies
* **recursiv** : Boolean, if true the search for controllers will be .. recursiv. Not recommanded for optimization.

### Using

Loop the following for each controller you want to be annotation-equiped.

1. Give a prefix for the controller (which will be prepend for every action route)
```php
/**
 * @Prefix /user
 */
class UserController {
```

2. Pimp your actions
```php
    /**
     * @Route /logoutnow
     */
    public function logoutAction(Application $app)
    {
        AbstractUserManager::disconnectCurrentUser($app);

        return $app->json();
    }
```
  This will mean a `GET` on `/user/logout` will call this action, which will be binded to `user.logout`
  
3. Test and enjoy!! 
  
## Available annotations

**Controller Level**
* *REQUIRED*  **Prefix** : Set the route prefix for every actions, also will be used for the default binding value. 

// TODO : Continuer 
**Actions Level**
* *REQUIRED*  **Route**
* *OPTIONAL*  **Method**
* *OPTIONAL*  **Bind**



## Feedback

Any sort of feedback is strongly encouraged and will be listened with love.

> "No more controller provider needed. Got my wife back. Love this" - George, Alabama.

> "This is awesome, my legal e-commerce website got 200% more purchases" - Ben, Thailandia.    
