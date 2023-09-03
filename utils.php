<?php
declare(strict_types=1);


function iterable_to_array(iterable $value): array {
    if ($value instanceof Traversable) {
        return iterator_to_array($value);
    }

    if (!is_array($value)) {
        throw new UnexpectedValueException('Iterable that is not Traversable nor array detected!');
    }

    return $value;
}
