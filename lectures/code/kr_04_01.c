#include <stdio.h>

void one(op)
    int op;
{
    int tn = 10;
    printf("One  op before %d\n",op);
    op = op - tn;
    printf("One  op after  %d\n",op);
}

int main() {
    int ma = 42;
    printf("Main ma before %d\n",ma);
    one(ma);
    printf("Main ma after  %d\n",ma);
}

