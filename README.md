# silex-simple-annotations

## When is the last time you dreamed of annotations for every controller route?
Silex Simple Annotations allows you to get rid of controller providers and have it done your(our) way.

Prepend your controller function with annotations. We will deal with the rest. 

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

## Why should I use this provider?

Currently, there seem to be only 2 providers after a quick Google search

* https://github.com/danadesrosiers/silex-annotation-provider
* https://github.com/dcousineau/orlex

They both use `Doctrine\Annotations` to parse annotations and seem to work.

So why?
* You hate Doctrine (strange)
* You want a simple syntax
* You are lazy and just want to provide your `/src` for controllers
* You may need an automatic documentation. *This will come in the future*

Why not?
* These other providers are probably faster for parsing
* I'm french

## What can i do? Is this FREE? 

### Registering

First, add this to your composer.json dependencies
```json
"require": {
    "other/packages...": "...",
    "nicolascharpentier/silex-simple-annotations": "dev-master"
  },
```

Then register the following service provider wherever you do so for your project:
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
     * @Route /logout
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

* **Controller Level**
  * **Prefix** *REQUIRED*
  * Sets the route prefix for every actions, also will be used for the default binding value. 
  * *eg.* /user

* **Actions Level**
  * **Route**  *REQUIRED* 
  * Sets the Route suffix.
  * *eg.* /edit/{id}
  * **Bind**  *OPTIONAL*
  * Sets the Route binding.
  * Default to Prefix + '.' + actionName (`str_replace($function_name, 'Action', '')`)
  * *eg.* user.edit
  * **Method**  *OPTIONAL*
  * Sets the request method.
  * Default to GET
  * Value(s) accepted GET POST PUT OPTIONS DELETE
  * *eg.* POST PUT

## TODOS
- ~~Get why the ordered list on this README just displays '1' at every step.~~
- ~~Support multiple methods for a Route~~
- Modify the Rules class to be more self explanable
- Generalize annotations usage when building Controllers
- Implement a cache system
- Start implementing the automated documentation

Ps: Not working on this at the moment, still checking for issues.

## Feedback & Contribution

Any sort of feedback is strongly encouraged and will be listened.

Also feel free to contribute or just ask for change, even the smallest


> "No more controller provider needed. Got my wife back. Love this" - George, Alabama.

> "This is awesome, my legal e-commerce website got 200% more purchases" - Ben, Thailandia.    
