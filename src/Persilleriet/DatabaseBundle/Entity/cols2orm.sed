s/[`)(,]//g
s/^[\t\ ]*\([a-zA-Z0-9].*\)\ /\*\ @ORM\\\\Column(name="\1",\ /
s/int[0-9]*/type="integer",\ nullable=false)/
s/varchar\([0-9]*\)/type="string", length="\1",\ nullable=false)/
