# ![finewsletter icon](ext_icon.gif) Finewsletter v1.1.1 - beta
## A really easy to use newsletter management extension for TYPO3 written in extbase & fluid.


### Problem to solve
THIS extension does not provide any newsletter delivery. It just manages these things:

* Subscribe (double opt-in)
* Unsubscribe (double opt-out / single with unique link)

Note: This extension is still under development, but the basics work well. That's why _beta_…


### Installation
1. Get the latest tagged version.
2. Install it via TYPO3 extension manager.
3. Include extension typoscript into your root template.
4. Setup a storage folder.
5. Include frontend plugin.

### Customize
I tried to create the extension as much flexible as i can. For this reason, you have a big typoscript object where you can modify things like error messages, validators and more. So multilanugage is no problem and you dont have to touch the locallang files.

#### Global settings
Currently you can only turn on/off the double opt-out option for
unsubscribe action.

```
global {
    double-opt-out = 1
}
```

#### Fields
You decide which forms you want to display and to validate. Below you can see the typoscript block between `plugin.tx_finewsletter.settings`. Language will be displayed as a selectbox. Enter multiple values with a `|` as delimiter. You should also declare a specific key infront of the label field, like en:english. In this case en will be stored in database and english will be displayed in selectbox. The advantage is, you can translate the labels in frontend without loosing consistency in your database.

```
fields {
      name {
          required = 1
          error = Please enter your name.
      }
      firstName {
          required = 0
          error = Please enter your first name.
      }
      lastName {
          required = 0
          error = Please enter your last name.
      }
      language {
          values = en:english | de:german | es:spanish
      }
}

```

#### Messages 
Even other error messages can be set via typoscript. Below a list with all available keys. _Hopefully self-explanatory, if not so ask me_

```
messages {
    subscribe {
        invalidEmail = Not a valid email address.
        emailExistsNotActive = Email already exists, but is not active. Another confirmation email has been sent.
        emailExists = Email already exists.
        confirmationSent = A confirmation email has been sent.
        invalidConfirmationLink = Your subscribe link seems invalid.
    }
    unsubscribe {
        invalidEmail = Not a valid email address.
        unknownEmail = Email address does not exist.
        confirmationSent = An unsubscribe confirmation email has been sent.
        invalidConfirmationLink = Your unsubscribe link seems invalid.
    }
}

```

#### Redirects
After each action there are two redirect options available. Either you stay on page and use a simple action redirect or you can jump to a specified pageUid. _In this example you will be redirected to page with uid 40 after successfull subscription. After successfull unsubscribe you will stay on page with different action (template)._
Since v1.1.0 you also can set up redirects after successful subscribe and unsubscribe

```
redirect {
	subscribe = 40
	unsubscribe =
	afterSubscribe = 1 
	afterUnsubscribe = 1
}
```

### Mail
Even mail settings will be configured in typoscript. If `test` flag is set to `true` your system won't send any mail. It just writes the text version into a file called mails.txt. Everything else should be self-explanatory.

```
mail {
    test = true
    senderName = Newsletter System
    senderEmail = newsletter@example.com

    subscribe {
        subject = Please confirm your subscription to our newsletter.
        templates { 
            html = EXT:finewsletter/Resources/Private/Templates/Mail/Subscribe.html
            plain = EXT:finewsletter/Resources/Private/Templates/Mail/Subscribe.txt
        }
    }
    unsubscribe {
        subject = Unsubscribe from our newsletter.
        templates {
            html = EXT:finewsletter/Resources/Private/Templates/Mail/Unsubscribe.html
            plain = EXT:finewsletter/Resources/Private/Templates/Mail/Unsubscribe.txt
        }
    }
} 

```


### Roadmap
* Add a nice backendmodule with some more functions.
* Adds optional JavaScript validation for several fields.
* Continuous improvements…


### Changes due to versions
v1.1.1 - beta

* Optimize translation handling for language field.

v1.1.0 - beta

* Added new verfiedAction template
* Added switch for double opt-out / single opt-out

v1.0.0 - beta

* Initial upload.
