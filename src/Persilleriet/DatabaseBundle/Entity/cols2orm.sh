# first argument is a file with lines like (copy-paste from mysqldump):
#
#  `id` int(11),
#  `name` varchar(55),
#  `acc_id` int(11),
#  `acc_number` varchar(30),
#  `def_cid` varchar(55),
#  `addr_id` int(11),
#  `street` varchar(100),
#  `city` varchar(80),
#  `zipcode` int(11),
#  `country` varchar(40),
#  `phone` varchar(17),
#  `email` varchar(55),
#  `addr_comment` varchar(700)
#
# second argument is the name of the db-table we are mapping like:
# 
# view_supplier
#
# third arg is the class name like:
#
# ViewSupplier

if [ $# -lt 3 ];
then 
	echo $0 column-definition.txt \"db_table\" \"PhpClass\"
else 

echo "<?php"
echo "namespace Persilleriet\DatabaseBundle\Entity;"
echo "use Doctrine\ORM\Mapping as ORM;"

echo "/**"
echo " *"
echo " * @ORM\Table(name=\"$2\")"
echo " * @ORM\Entity"
echo " */"
echo "class $3 {"

sed -f cols2orm.sed < $1 |\

while read orm_annotation;
do 
	echo 
	echo "/**"
	echo " $orm_annotation"
	echo " */"
	echo private \$$( echo $orm_annotation |sed -e 's/.*name="\([^"]*\)".*/\1/' -e 's/_\([a-z]\)/\u\1/')\;
done

## 
## generate fields
##
cut -d\` -f 2 $1 | \
#grep private $FIELDS |sed 's/.*\$\(.*\);/\1/' |  \
while read i; 
do 
	i=$(echo $i | sed -e 's/_\([a-z]\)/\u\1/')
	echo "public function get${i^}()			{ return \$this->$i;}"
	echo "public function set${i^}(\$$i) 	{\$this->$i = \$$i;}"
done |\
sort

echo "}"
echo "?>"

fi
