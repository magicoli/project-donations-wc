. $(dirname $0)/svnrc || exit $?

cd $dev && pwd || exit $?
release=$(echo $(grep -h "Version:" *.php | head -1 | cut -d : -f 2))
cd $svn && pwd || exit $?
ls -d tags/$release 2>/dev/null && echo "release $release already deployed" && exit

echo sync $release from $dev to $svn
rsync --delete -Wavz $dev/ $svn/trunk/ --exclude-from $dev/.distignore --exclude-from $dev/.wpignore --exclude assets/ || exit $?
rsync --delete -Wavz $dev/assets/ $svn/assets/ || exit $?
rsync --delete -Wavz trunk/ tags/$release/
svn add tags/$release/
svn status | grep "^\?" | while read f file
do
  svn add "$file"
done
svn status
echo
echo "# check status above and if everything is fine, execute:

cd $svn
svn ci -m \"version $release\""
