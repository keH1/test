class User extends Model
{
    // Предполагается, что поле 'balance' уже определено в миграциях

    /**
     * Списывает указанную сумму с баланса пользователя.
     *
     * @param float $amount
     * @return bool
     * @throws \Exception
     */
    public function debitBalance(float $amount): bool
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Сумма для списания должна быть положительной.');
        }

        return DB::transaction(function () use ($amount) {
            $user = self::where('id', $this->id)->lockForUpdate()->first();

            if ($user->balance < $amount) {
                throw new \Exception('Недостаточно средств на балансе.');
            }

            $user->balance -= $amount;
            return $user->save();
        });
    }

    /**
     * Зачисляет указанную сумму на баланс пользователя.
     *
     * @param float $amount
     * @return bool
     * @throws \Exception
     */
    public function creditBalance(float $amount): bool
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Сумма для зачисления должна быть положительной.');
        }

        return DB::transaction(function () use ($amount) {
            $user = self::where('id', $this->id)->lockForUpdate()->first();

            $user->balance += $amount;
            return $user->save();
        });
    }
}
