<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Utility;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class ArrayUtilitySpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Utility\ArrayUtility');
    }


    public function it_merges_2_arrays()
    {
        $array1 = [1, 'tow', 3, 'four'];
        $array2 = [5, 'six', 7, 'eight'];

        $array = $this->merge($array1, $array2);
        $array->shouldBeArray();
        $array->shouldBe([1, 'tow', 3, 'four', 5, 'six', 7, 'eight']);
    }

    public function it_merges_greater_than_2_arrays()
    {
        $array1 = [1, 'tow', 3, 'four'];
        $array2 = [5, 'six', 7, 'eight'];
        $array3 = [9, 'ten'];

        $array = $this->merge($array1, $array2, $array3);
        $array->shouldBeArray();
        $array->shouldBe([1, 'tow', 3, 'four', 5, 'six', 7, 'eight', 9, 'ten']);
    }

    public function it_merges_some_arrays_has_key()
    {
        $array1 = ['first' => 1, 'second' => 2];
        $array2 = ['third' => 3];

        $array = $this->merge($array1, $array2);
        $array->shouldBe(['first' => 1, 'second' => 2, 'third' => 3]);
    }
    
    public function it_merges_some_arrays_mixing_key()
    {
        $array1 = ['first' => 1, 'foo', 'second' => 2];
        $array2 = ['third' => 3, 'bar', 'zoo'];

        $array = $this->merge($array1, $array2);
        $array->shouldBe(['first' => 1, 'foo', 'second' => 2, 'third' => 3, 'bar', 'zoo']);
    }
}

