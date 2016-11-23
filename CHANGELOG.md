## 0.8.1

Bugfix release.

  * Fix stroke and fill is not drawn correctly #18 @hidakatsuya @mikkame
  * 0.8.x never supports `.tlf` generated with Thinreports Editor 0.9+ #19 @hidakatsuya

## 0.8.0

This is the FIRST major release, and includes the following change:

  * Support multiple layouts: #8
  * Add way for pre-converting built-in unicode fonts: #7 #16
  * Fix solid line is not drawn correctly: #13 @mikkame @maynbow

### Support multiple layouts

```php
<?php
$report = Thinreports\Report('default_layout.tlf');

$report->addPage();                   # Use 'default_layout.tlf'
$report->addPage('other_layout.tlf'); # Use 'other_layout.tlf'
```

See [#8](https://github.com/thinreports-php/thinreports-php/pull/8) for feature details.

### Add way for pre-converting built-in unicode fonts

TCPDF generates a special font from unicode font when render the text at first time, and the generation process takes a little time.

You can build the unicode font in advance by executing the following method.

```php
<?php
Thinreports\Generator\PDF\Font::build();
```

See [#7](https://github.com/thinreports-php/thinreports-php/pull/7), [#16](https://github.com/thinreports-php/thinreports-php/pull/16) for further details.

### Fix solid line is not drawn correctly

See [#13](https://github.com/thinreports-php/thinreports-php/pull/16) for further details.

## 0.8.0-alpha2

This release includes the BIG changes for support of PHP 5.3 and 5.4, 5.5, 5.6, 7, and also includes the following change:

  * Support PHP5.3 or higher
  * **Deprecate** the way for adding a page using anonymous function, like below:
    ```
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
