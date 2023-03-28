# Custom Contact Form

This module allows you to create custom contact form

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is CustomContactForm.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/custom-contact-form-module:~1.0
```

## Usage

To create a new custom contact form : 
go to ***your-site***/admin/module/CustomContactForm

Each form have : 
* a title
* a code
* a field configuration (look behind)
* the receiver email

Example basic of field configuration (Json):

```
[
    {
        "label": "Nom",
        "required": false
    },
    {
        "label": "Prénom",
        "required": true
    }
]
```