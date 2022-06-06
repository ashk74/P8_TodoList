<?php

namespace App\Tests\Traits;

use Symfony\Component\Validator\Validator\ValidatorInterface;

trait CustomAssert
{
    /**
     * Check constraint validation errors
     *
     * @param \App\Entity\User $user
     * @param integer $expectedErrors
     *
     * @return void
     */
    public function assertPropertyConstraint($entity, int $expectedErrors)
    {
        self::bootKernel();
        $errors = $this->getContainer()->get(ValidatorInterface::class)->validate($entity);
        $errorMessages = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $errorMessages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($expectedErrors, $errors, implode(', ', $errorMessages));
    }
}
