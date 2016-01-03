# silex-simple-annotations

## When is the last night you dreamed of annotations for every controller route ?
Silex Simple Annotations allows you to get ride of controller providers and have it done your(our) way.

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

## Why should I use this provider ?

Currently there are 2 cools providers Google gave me

https://github.com/danadesrosiers/silex-annotation-provider

https://github.com/dcousineau/orlex

They both use Doctrine\Annotations to parse Annots and seem to work.

So why?
* You hate Doctrine (strange)
* You want a simple syntax
* You are lazy and just want to provide your /src for controllers
* You may need an automatic documentation. *This will come in the futur*

Why not?
* These other providers are probably faster for parsing
* Im french

## What can i do ? Is this FREE ? 

### Registering

First, add this to your composer.json dependencies
```json
"require": {
    "other/packages...": "...",
    "nicolascharpentier/silex-simple-annotations": "dev-master"
  },
```

Then in the registering-services-part of your project, add these lines
```php
$app->register(new SilexSimpleAnnotations\AnnotationsServiceProvider(), array(
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
    
    /**
     * @Route /stalk/{id}
     * @Bind user.stalkhim
     */
    public function stalkAction(Application $app, $id)
    {
        AbstractUserManager::disconnectCurrentUser($app);

        return $app->json();
    }
```
  This will mean
- a `GET` on `/user/logout` will call the `logoutAction`, which will be binded to `user.logout`
- a `GET` on `/user/stalk/1` will call the `stalkAction`, with `$id === '1'` and the bind on `user.stalkhim`
  
  
3. Test and enjoy!! 
  
## Available annotations

**Controller Level**
* *REQUIRED*  **Prefix** : Set the route prefix for every actions, also will be used for the default binding value. 

**Actions Level**
* *REQUIRED*  **Route** : Set the Route suffix
* *OPTIONAL*  **Method** : Sets the Route Method. Default to GET
* *OPTIONAL*  **Bind** : Sets the Route binding. Default to ctrlPrefix + actionName

## TODOS
- Get why the ordered list on this README just displays '1' at every step.
- Support multiple methods for a Route
- Start implementing the automated documentation

Ps: Cant work on this all next week long (4-10 jan), will still check for issues.

## Feedback & Contribution

Any sort of feedback is strongly encouraged and will be listened.

Also feel free to contribute or just ask for change, even the smallest


> "No more controller provider needed. Got my wife back. Love this" - George, Alabama.

> "This is awesome, my legal e-commerce website got 200% more purchases" - Ben, Thailandia.    
