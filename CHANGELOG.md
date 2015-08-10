## 0.8.0-alpha2

This release includes the BIG changes for support of PHP 5.3 and 5.4, 5.5, 5.6, 7, and also includes the following change:

  * Support PHP5.3 or higher
  * **Deprecate** the way for adding a page using anonymous function, like below:
```php
$report->addPage(function ($page) {
    $page->item('price')->setValue(1000);
});
```
  * Testing for basic features

## 0.8.0-alpha1

This release is Concept Version for providing the first experience of generating a PDF using thinreports-php.

  * Support PHP 5.6 only
  * Minimum implementation and testing

See [Milestones](https://github.com/thinreports-php/thinreports-php/milestones) for feature release plan.
