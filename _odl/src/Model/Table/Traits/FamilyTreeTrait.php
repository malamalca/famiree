<?php
namespace App\Model\Table\Traits;

use App\Model\Entity\Profile;
use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;

/**
 * FamilyTree Behavior for CakePHP 1.2
 *
 * @copyright     Copyright 2008, Miha Nahtigal (http://www.nahtigal.com)
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 *
 *
 */
trait FamilyTreeTrait
{
    /**
     * ghost_id variable
     *
     * @var int
     */
    private $ghost_id = -1;
    /**
     * dx variable specifies width of single profile; spacings included
     *
     * @var int
     */
    private $dx = 6;
    /**
     * dy variable specifies tree line height
     *
     * @var int
     */
    private $dy = 4;

    /**
     * Unions cache
     *
     * @var array
     */
    private $_unions = [];
    /**
     * Child to union cache
     *
     * @var array
     */
    private $_c2u = [];
    /**
     * Parent to union cache
     *
     * @var array
     */
    private $_p2u = [];
    /**
     * Gender cache
     *
     * @var array
     */
    private $_g = [];

    /**
     * tree method
     *
     * Fetch tree for specified profile
     *
     * @param mixed $id Profile id
     * @param int $depth Tree depthd
     * @return array Unions and Profiles with calculated positions for XML tree generation
     */
    public function tree($id, $depth = 100)
    {
        // initialize defaults
        $this->ghost_id = -1;

        $unions = [];
        $profiles = [];

        $parent_unions = [];
        $parent_profiles = [];

        $this->__buildCache();

        // I. CHAPTER: downwards
        //  There are three possible situations:
        //      1. simplest: Profile is not child in any other union
        //      2. simple: There are no parents for this profile
        //      3. classic: There are parents with their siblings
        //  First two are almost the same - profiles line is processed.
        //  The third one is different. We start processing at parent level
        //
        //  Steps to take:
        //      1. fetch all siblings and order them by sort_order than by id
        //      2. add all unions (spouses) for every profile order them by date
        //      3. process children tree foe each union; return child tree width
        //      4. reposition siblings/spouses profiles according to child tree width

        // Does requested profile have parents and do they have any siblings?

        if ($parents = $this->__fetchParents($id)) {
            // Situation 3 :: initialization
            $main_union = $this->__fetchUnionOfChild($id);

            $process_unions = [];
            if (!empty($parents['dad'])) {
                $dads_siblings = [];
                if ($depth > 0) {
                    $dads_siblings = $this->__fetchSiblings($parents['dad'], false);
                }
                $dads_siblings[] = $parents['dad'];
                $process_unions = $dads_siblings;
            }

            if (!empty($parents['mom'])) {
                $moms_siblings = [];
                if ($depth > 0) {
                    $moms_siblings = $this->__fetchSiblings($parents['mom'], false);
                }
                if (empty($dads_siblings)) {
                    $process_unions[] = $parents['mom'];
                }
                $process_unions = array_merge($process_unions, $moms_siblings);
            }

            $this->__recurseChildren($process_unions, 0, $unions, $profiles, [
                'x' => 0,
                'y' => $this->dy,
                'main_union' => $main_union,
                'direct_relative_id' => [$parents['dad'], $parents['mom']],
                'maxDepth' => $depth
            ]);
            $this->__bulkReadProfiles($profiles, array_keys($profiles));

            // Center parents right above main profile
            $delta_p =
                ($profiles[$unions[$main_union]['p'][0]]['Profile']['x'] +
                 $profiles[$unions[$main_union]['p'][1]]['Profile']['x']) / 2 -
                 $profiles[$id]['Profile']['x'];
            $profiles[$unions[$main_union]['p'][0]]['Profile']['x'] -= $delta_p;
            $profiles[$unions[$main_union]['p'][1]]['Profile']['x'] -= $delta_p;

            // Process parents tree
            $dads_width = 0;
            if (!empty($parents['dad'])) {
                $dads_width = $this->__recurseParents($parents['dad'], 0, $parent_unions, $parent_profiles, [
                    'x' => (int)($this->dx / 2),
                    'y' => $this->dy * 2,
                    'width' => 0,
                    'maxDepth' => $depth
                ]);
            }
            // move dad's tree to the left
            foreach ($parent_profiles as $profile_id => $profile) {
                $parent_profiles[$profile_id]['Profile']['x'] -= $dads_width;
            }

            // proces mom's tree
            if (!empty($parents['mom'])) {
                $this->__recurseParents($parents['mom'], 0, $parent_unions, $parent_profiles, [
                    'x' => (int)($this->dx / 2),
                    'y' => $this->dy * 2,
                    'width' => 0,
                    'maxDepth' => $depth
                ]);
            }
            $this->__bulkReadProfiles($parent_profiles, array_keys($parent_profiles));
        } else {
            // Situation 1 or Situation 2
            $siblings = $this->__fetchSiblings($id);
            if (empty($siblings)) {
                // Situation 1 (profile is not a child in any union)
                $process_unions = [$id];
                $this->__recurseChildren($process_unions, 0, $unions, $profiles, [
                    'x' => 0,
                    'y' => 0,
                    'direct_relative' => true,
                    'maxDepth' => $depth
                ]);
                $this->__bulkReadProfiles($profiles, array_keys($profiles));
            } else {
                // Situation 2 (no parents in profile's union)
                $main_union = $this->__fetchUnionOfChild($id);

                $this->__recurseChildren($siblings, 0, $unions, $profiles, [
                    'x' => 0,
                    'y' => 0,
                    'main_union' => $main_union,
                    'direct_relative' => true,
                    'maxDepth' => $depth
                ]);
                $this->__bulkReadProfiles($profiles, array_keys($profiles));

                // Situation 2 :: Create parents
                // Create ghost profiles for parents, position parents, add main union
                $mom = $this->__addGhost(
                    $profiles,
                    $profiles[$id]['Profile']['x'] + (int)($this->dx / 2),
                    $this->dy,
                    [
                        'method' => 'add_parent',
                        'ref' => $id,
                        'direct_relative' => true
                    ]
                );

                $dad = $this->__addGhost(
                    $profiles,
                    $profiles[$id]['Profile']['x'] - (int)($this->dx / 2),
                    $this->dy,
                    [
                        'method' => 'add_parent',
                        'ref' => $id,
                        'direct_relative' => true
                    ]
                );

                $unions[$main_union] = $this->_unions[$main_union];
                $unions[$main_union]['p'][] = $dad;
                $unions[$main_union]['p'][] = $mom;
            }
        }

        // OUTSIDE MAIN SITUATIONS - applies for all:
        // Center main profile on position 0,0 (move all profiles according to center one)
        $delta_x = $profiles[$id]['Profile']['x'];
        foreach ($profiles as $p_id => $p) {
            $profiles[$p_id]['Profile']['x'] -= $delta_x;
        }

        return ['u' => $unions + $parent_unions, 'p' => $profiles + $parent_profiles];
    }

    /**
     * __recurseChildren method
     *
     * Recursive function for processing children
     *
     * @param  array $children Children array
     * @param  int $depth Processing depth
     * @param  array $unions Unions array
     * @param  array $profiles Profiles array
     * @param  array $options Options array
     * @return float|int Current width
     */
    private function __recurseChildren($children, $depth, &$unions, &$profiles, $options = [])
    {
        // initialization
        $current_width = 0;

        // process each profile
        foreach ($children as $profile_id) {
            // cache profile gender; avoid unnecessary multiple function calls
            $profile_g = $this->__gender($profile_id);

            // fetch all unions in which profile is parent (most commonly it will be one or none)
            if ($profile_unions = $this->__fetchUnionsOfParent($profile_id)) {
                // counter
                $i = 1;

                // move main union to front or to back according to gender
                if (!empty($options['main_union']) && isset($profile_unions[$options['main_union']])) {
                    $profile_unions = array_diff_key($profile_unions, [$options['main_union'] => $profile_id]);

                    if ($profile_g == 'm') {
                        $profile_unions = [$options['main_union'] => $profile_id] + $profile_unions;
                    } else {
                        $profile_unions = $profile_unions + [$options['main_union'] => $profile_id];
                    }

                    // this few lines is for partners of second spouse
                    if ($spouse_id = $this->__fetchSpouse($profile_id, $options['main_union'])) {
                        $spouse_unions = $this->__fetchUnionsOfParent($spouse_id, $profile_id);
                        if (!empty($spouse_unions)) {
                            if ($profile_g == 'm') {
                                $profile_unions = $spouse_unions + $profile_unions;
                            } else {
                                $profile_unions = $profile_unions + $spouse_unions;
                            }
                        }
                    }
                }

                // profile has at least one union
                // process each union further down
                $spouse_union_count = 2;
                foreach ($profile_unions as $profile_union_id => $real_profile_id) {
                    // add union to list
                    $unions[$profile_union_id] = $this->_unions[$profile_union_id];
                    if ($depth > $options['maxDepth']) {
                        unset($unions[$profile_union_id]['c']);
                    }

                    if (!empty($options['main_union']) && ($options['main_union'] == $profile_union_id)) {
                        $options['x'] += (int)($this->dx / 2);
                    }

                    $base_width = $this->dx;
                    if ($children = $this->__fetchChildren($real_profile_id, $profile_union_id)) {
                        if ($depth <= $options['maxDepth']) {
                            $base_width = $this->__recurseChildren(
                                $children,
                                $depth + 1,
                                $unions,
                                $profiles,
                                [
                                    'x' => $options['x'] + $current_width,
                                    'y' => $options['y'] - $this->dy,
                                    'direct_relative' => !empty($options['direct_relative']) ||
                                        (isset($options['main_union']) && $options['main_union'] == $profile_union_id),
                                    'maxDepth' => $options['maxDepth']
                                ]
                            );
                        } else {
                            // just read children profiles
                        }
                    }

                    // set position for profile and spouses
                    if (
                        (
                            // this is the case on central union where we have combined
                            // monthers and fathers secondary spouses
                            !empty($options['main_union']) &&
                            isset($profile_unions[$options['main_union']]) &&
                            ($options['main_union'] != $profile_union_id)
                        ) ||
                        (
                            // this is general rule
                            // first part is negation of upper onditions
                            (empty($options['main_union']) ||
                            (
                                !empty($options['main_union']) &&
                                !isset($profile_unions[$options['main_union']])
                            )) &&

                            ((count($profile_unions) > 1) &&
                            ((($profile_g == 'm') &&
                            ($i > 1)) ||
                            ((($profile_g == 'f') &&
                            ($i < count($profile_unions))))))
                        )
                    ) {
                        // this is the case where only single parent is displayed
                        // eg. multiple marriages
                        $spouse_position = $options['x'] + $current_width + (int)($base_width / 2) - (int)($this->dx / 2);
                        $unions[$profile_union_id]['spouse_count'] = $spouse_union_count;
                        $spouse_union_count++;
                    } else {
                        // must be double width
                        if ($base_width < $this->dx * 2) {
                            $base_width = $this->dx * 2;
                        }

                        // make bigger space for main union
                        if (
                            !empty($options['main_union']) &&
                            ($options['main_union'] == $profile_union_id)
                        ) {
                            $base_width += (int)($this->dx / 2);
                        }

                        // gender positioning factor;
                        if ($profile_g == 'm') {
                            $dx_f = $this->dx;
                        } else {
                            $dx_f = 0;
                        }

                        // oncle or ount have been married only once
                        // or case where two parents are displayed together
                        // add base profile
                        $this->__addProfile(
                            $profiles,
                            $real_profile_id,
                            $options['x'] + $current_width + (int)($base_width / 2) - $dx_f,
                            $options['y'],
                            [
                                'direct_relative' => !empty($options['direct_relative']) ||
                                (isset($options['direct_relative_id']) && in_array($real_profile_id, (array)$options['direct_relative_id']))
                            ]
                        );

                        $spouse_position = $options['x'] + $current_width + (int)($base_width / 2) - $this->dx + $dx_f;

                        // when having only single child, reposition child a bit
                        if ($single_child_id = $this->__bottomMostChildInUnion($profile_union_id)) {
                            if ($depth <= $options['maxDepth']) {
                                $profiles[$single_child_id]['Profile']['x'] += (int)($this->dx / 2);
                            }
                        }
                    }

                    // process spouse (always)
                    if ($spouse_id = $this->__fetchSpouse($real_profile_id, $profile_union_id)) {
                        // normal spouse
                        $this->__addProfile(
                            $profiles,
                            $spouse_id,
                            $spouse_position,
                            $options['y'],
                            [
                                'direct_relative' =>
                                (isset($options['direct_relative_id']) && in_array($real_profile_id, (array)$options['direct_relative_id']))
                            ]
                        );
                        // add tree icon if sposuse has more than one marriage or
                        // is child in any union and is not main union (which has already all that
                        // included in tree
                        if (
                            ($this->__fetchUnionOfChild($spouse_id) || (count($this->__fetchUnionsOfParent($spouse_id)) > 1))
                            &&
                            (empty($options['main_union']) || ($options['main_union'] != $profile_union_id))
                        ) {
                            $profiles[$spouse_id]['Profile']['showPrune'] = '+';
                        }
                    } else {
                        // add ghost spouse to profiles
                        $spouse_id = $this->__addGhost(
                            $profiles,
                            $spouse_position,
                            $options['y'],
                            [
                                'method' => 'add_partner',
                                'ref' => $real_profile_id
                            ]
                        );

                        // add ghost profile to unions
                        $unions[$profile_union_id]['p'][] = $spouse_id;
                    }

                    // increment position and counters
                    $current_width += $base_width;
                    $i++;
                }
            } else {
                // no unions for this profile; this profile is bottom-most
                // this profile cannot be ghost
                $this->__addProfile(
                    $profiles,
                    $profile_id,
                    $options['x'] + $current_width,
                    $options['y'],
                    [
                        'direct_relative' => isset($options['direct_relative']) ? $options['direct_relative'] : false
                    ]
                );

                $current_width += $this->dx;
            }
        } // foreach (profiles as $profile_id) - MAIN LOOP

        return $current_width;
    }

    /**
     * __recurseParents method
     *
     * Recursive function for processing parents
     *
     * @param int $profile_id Profile id.
     * @param int $depth Tree depth.
     * @param array $unions Unions array.
     * @param array $profiles Profiles array.
     * @param array $options Options array.
     * @return float|int Current width
     */
    private function __recurseParents($profile_id, $depth, &$unions, &$profiles, $options)
    {
        // initialization
        $current_width = 0;

        if ($depth >= $options['maxDepth']) {
            return 0;
        }

        $parent_union_id = $this->__fetchUnionOfChild($profile_id);

        if (isset($this->_unions[$parent_union_id])) {
            $unions[$parent_union_id] = $this->_unions[$parent_union_id];
        }

        if ($parents = $this->__fetchParents($profile_id)) {
            $base_profiles = ['dad' => [], 'mom' => []];
            $siblings = ['dad' => [], 'mom' => []];
            $siblings_widths = ['dad' => 0, 'mom' => 0];
            $upper_widths = ['dad' => 0, 'mom' => 0];

            // 1. STEP: width calculation
            foreach ($parents as $parent => $parent_id) {
                if ($parent_id) {
                    // regular parent - fetch siblings, include self
                    $siblings[$parent] = $this->__fetchSiblings($parent_id);
                    // if empty siblings, we're on the top of the tree; process them artificially
                    if (empty($siblings[$parent]) || ($depth + 1 == $options['maxDepth'])) {
                        $siblings[$parent] = [$parent_id];
                    }

                    // calculate width of siblings and their spouses
                    foreach ($siblings[$parent] as $sibling_id) {
                        $siblings_widths[$parent] += $this->dx;

                        $spouse_unions = array_keys($this->__fetchUnionsOfParent($sibling_id));
                        $spouse_unions = array_diff($spouse_unions, [$parent_union_id]);
                        $siblings_widths[$parent] += count($spouse_unions) * $this->dx;
                    }

                    // recurse into parents only if parent is child in any of unions
                    if ($this->__fetchUnionOfChild($parent_id)) {
                        $upper_widths[$parent] = $this->__recurseParents(
                            $parent_id,
                            $depth + 1,
                            $unions,
                            $base_profiles[$parent],
                            [
                                'x' => $options['x'],
                                'y' => $options['y'] + $this->dy,
                                'width' => $siblings_widths[$parent],
                                'maxDepth' => $options['maxDepth']
                            ]
                        );
                    }
                } else {
                    $siblings_widths[$parent] = $this->dx;
                }
            } // finished 1. STEP: width calculation

            $upper_width = $upper_widths['dad'] + $upper_widths['mom'];
            $siblings_width = $siblings_widths['dad'] + $siblings_widths['mom'];

            // calculate total width
            $current_width = 0;
            if ($upper_widths['dad'] > $siblings_widths['dad']) {
                $current_width += $upper_widths['dad'];
            } else {
                $current_width += $siblings_widths['dad'];
            }
            if ($upper_widths['mom'] > $siblings_widths['mom']) {
                $current_width += $upper_widths['mom'];
            } else {
                $current_width += $siblings_widths['mom'];
            }

            // move mom's tree to the left of dads
            $moms_delta = $siblings_widths['dad'];
            if ($upper_widths['dad'] > $siblings_widths['dad']) {
                $moms_delta = $upper_widths['dad'];
            }
            if (!empty($base_profiles['mom'])) {
                foreach ($base_profiles['mom'] as $k => $p) {
                    $base_profiles['mom'][$k]['Profile']['x'] += $moms_delta;
                }
            }

            // process placement
            $tree_delta = 0;
            if ($options['width'] > $siblings_width) {
                // move both trees to the left
                $tree_delta = ($options['width'] - $siblings_width) / 2;
                if (!empty($base_profiles['dad'])) {
                    foreach ($base_profiles['dad'] as $k => $p) {
                        $base_profiles['dad'][$k]['Profile']['x'] += $tree_delta;
                    }
                }
                if (!empty($base_profiles['mom'])) {
                    foreach ($base_profiles['mom'] as $k => $p) {
                        $base_profiles['mom'][$k]['Profile']['x'] += $tree_delta;
                    }
                }
            }

            // add to profiles
            $profiles = $profiles + $base_profiles['dad'] + $base_profiles['mom'];

            $current_x = $options['x'] + $tree_delta;
            // 2. STEP: display and adjust profiles
            foreach ($parents as $parent => $parent_id) {
                // add centering factor when upper width is larger than mine
                if ($upper_widths[$parent] > $siblings_widths[$parent]) {
                    $current_x += ($upper_widths[$parent] - $siblings_widths[$parent]) / 2;
                }

                if ($parent_id) {
                    // center profiles and display them
                    foreach ($siblings[$parent] as $sibling_id) {
                        if ($this->__gender($sibling_id) == 'm') {
                            $this->__addProfile(
                                $profiles,
                                $sibling_id,
                                $current_x,
                                $options['y'],
                                [
                                    'direct_relative' => in_array($sibling_id, $parents)
                                ]
                            );
                            $current_x += $this->dx;
                        }

                        $spouse_unions = array_keys($this->__fetchUnionsOfParent($sibling_id));
                        $spouse_unions = array_diff($spouse_unions, [$parent_union_id]);

                        if (!empty($spouse_unions)) {
                            $spouse_union_count = 2;
                            foreach ($spouse_unions as $spouse_union_id) {
                                $unions[$spouse_union_id] = $this->_unions[$spouse_union_id];
                                $unions[$spouse_union_id]['c'] = [];
                                $unions[$spouse_union_id]['spouse_count'] = $spouse_union_count;

                                if ($spouse_id = $this->__fetchSpouse($sibling_id, $spouse_union_id)) {
                                    $this->__addProfile(
                                        $profiles,
                                        $spouse_id,
                                        $current_x,
                                        $options['y']
                                    );
                                    // add tree icon if sposuse has more than one marriage or
                                    // is child in any union
                                    if (
                                        ($this->__fetchUnionOfChild($spouse_id) ||
                                        (count($this->__fetchUnionsOfParent($spouse_id)) > 1))
                                    ) {
                                        $profiles[$spouse_id]['Profile']['showPrune'] = '+';
                                    }
                                } else {
                                    $ghost_id = $this->__addGhost(
                                        $profiles,
                                        $current_x,
                                        $options['y'],
                                        [
                                            'method' => 'add_partner',
                                            'ref' => $sibling_id,
                                        ]
                                    );
                                    $unions[$spouse_union_id]['p'][] = $ghost_id;
                                }
                                $current_x += $this->dx;
                                $spouse_union_count++;
                            }
                        }

                        if ($this->__gender($sibling_id) == 'f') {
                            $this->__addProfile(
                                $profiles,
                                $sibling_id,
                                $current_x,
                                $options['y'],
                                [
                                    'direct_relative' => in_array($sibling_id, $parents)
                                ]
                            );
                            $current_x += $this->dx;
                        }

                        // add tree icon profile isnt in main line and have children
                        if (($sibling_id != $parent_id) && count($this->__fetchChildren($sibling_id)) > 0) {
                            $profiles[$sibling_id]['Profile']['showPrune'] = '+';
                        }
                    }
                } else {
                    // this parent doesnt exist; create ghost
                    $ghost_id = $this->__addGhost(
                        $profiles,
                        $current_x,
                        $options['y'],
                        [
                            'method' => 'add_parent',
                            'ref' => $profile_id,
                            'direct_relative' => true,
                        ]
                    );
                    $unions[$parent_union_id]['p'][] = $ghost_id;
                }

                // adjust x for mom
                $current_x = $options['x'] + $tree_delta + $moms_delta;
            }
        } else {
            // no parents, create two ghosts
            // if width below > 2 * dx, center them
            if ($options['width'] > 2 * $this->dx) {
                $current_width += $options['width'];
                $center_position = (int)($current_width / 2);
            } else {
                $current_width += 2 * $this->dx;
                $center_position = $this->dx;
            }

            $ghost_id = $this->__addGhost(
                $profiles,
                $options['x'] + $current_width - $center_position - $this->dx,
                $options['y'],
                [
                    'method' => 'add_parent',
                    'ref' => $profile_id,
                    'direct_relative' => true
                ]
            );
            $unions[$parent_union_id]['p'][] = $ghost_id;

            $ghost_id = $this->__addGhost(
                $profiles,
                $options['x'] + $current_width - $center_position,
                $options['y'],
                [
                    'method' => 'add_parent',
                    'ref' => $profile_id,
                    'direct_relative' => true
                ]
            );
            $unions[$parent_union_id]['p'][] = $ghost_id;
        }

        return $current_width;
    }

    /**
     * buildCache method
     *
     * Build cache creates tree cache for faster access
     *
     * @access public
     * @return void
     */
    private function __buildCache()
    {
        Cache::delete('Units.tree');

        $units = Cache::remember('Units.tree', function () {
            return TableRegistry::get('Units')->find()
                ->select(['Units.union_id', 'Units.profile_id', 'Units.kind', 'Profiles.g'])
                ->contain(['Profiles'])
                ->order(['Units.union_id', 'Units.kind DESC', 'Units.sort_order', 'Units.id'])
                ->all();
        });

        // union cache
        $this->_unions = [];

        // "fetchAsChild" cache
        $this->_c2u = [];

        // "fetchAsParent" cache
        $this->_p2u = [];

        // gender cache
        $this->_g = [];

        foreach ($units as $u) {
            $union_id = $u->union_id;
            $profile_id = $u->profile_id;

            $this->_unions[$union_id][$u->kind][] = $profile_id;
            $this->_g[$profile_id] = isset($u->profile->g) ? $u->profile->g : '';

            if ($u->kind == 'c') {
                $this->_c2u[$profile_id][$union_id] = &$this->_unions[$union_id];
            } else {
                $this->_p2u[$profile_id][$union_id] = &$this->_unions[$union_id];
            }
        }
    }

    /**
     * __addProfile method
     *
     * @param array $profiles Profile list.
     * @param int $profile_id Profile id.
     * @param int $x X position.
     * @param int $y Y position.
     * @param array $options Options array.
     * @return void
     */
    private function __addProfile(&$profiles, $profile_id, $x, $y, $options = [])
    {
        $profiles[$profile_id]['Profile']['id'] = $profile_id;
        $profiles[$profile_id]['Profile']['x'] = $x;
        $profiles[$profile_id]['Profile']['y'] = $y;
        if (!empty($options['direct_relative'])) {
            $profiles[$profile_id]['Profile']['d_r'] = true;
        }
    }

    /**
     * __gender method
     *
     * @param int $profile_id Profile id
     * @access private
     * @return bool|string
     */
    private function __gender($profile_id)
    {
        if (isset($this->_g[$profile_id])) {
            return $this->_g[$profile_id];
        }

        return false;
    }

    /**
     * __union method
     *
     * This method returns union_id for two parents
     *
     * @param int $profile1_id First profile id
     * @param int $profile2_id Second profile id
     * @access private
     * @return bool|int
     */
    private function __union($profile1_id, $profile2_id)
    {
        $union = array_intersect(
            array_keys($this->_p2u[$profile1_id]),
            array_keys($this->_p2u[$profile2_id])
        );
        if (!empty($union)) {
            return reset($union);
        }

        return false;
    }

    /**
     * __bottomMostChildInUnion method
     *
     * @param int $union_id Union id
     * @access private
     * @return mixed
     */
    private function __bottomMostChildInUnion($union_id)
    {
        if (isset($this->_unions[$union_id]['c']) && (count($this->_unions[$union_id]['c']) == 1)) {
            // this child must also not be parent in any union
            $child_id = $this->_unions[$union_id]['c'][0];
            if (!isset($this->_p2u[$child_id])) {
                return $child_id;
            }
        }

        return false;
    }

    /**
     * __fetchUnionOfChild method
     *
     * @param int $profile_id Profile id.
     * @return bool|array
     */
    private function __fetchUnionOfChild($profile_id)
    {
        if (isset($this->_c2u[$profile_id])) {
            foreach ($this->_c2u[$profile_id] as $union_id => $union) {
                return $union_id;
            }
        }

        return false;
    }

    /**
     * __fetchUnionsOfParent method
     *
     * @param int $profile_id Profile id.
     * @param bool $exclude_profile_id Exclude base profile.
     * @return array
     */
    private function __fetchUnionsOfParent($profile_id, $exclude_profile_id = false)
    {
        $ret = [];
        if (isset($this->_p2u[$profile_id])) {
            foreach ($this->_p2u[$profile_id] as $union_id => $union) {
                if (($exclude_profile_id == false) || !in_array($exclude_profile_id, $union['p'])) {
                    $ret[$union_id] = $profile_id;
                }
            }
        }

        return $ret;
    }

    /**
     * __fetchSpouse method
     *
     * @param int $profile_id Profile id
     * @param int $union_id Union id
     * @return bool|int
     */
    private function __fetchSpouse($profile_id, $union_id)
    {
        if (!empty($this->_p2u[$profile_id][$union_id]['p'][0]) && ($this->_p2u[$profile_id][$union_id]['p'][0] != $profile_id)) {
            return $this->_p2u[$profile_id][$union_id]['p'][0];
        } elseif (!empty($this->_p2u[$profile_id][$union_id]['p'][1]) && ($this->_p2u[$profile_id][$union_id]['p'][1] != $profile_id)) {
            return $this->_p2u[$profile_id][$union_id]['p'][1];
        }

        return false;
    }

    /**
     * __fetchChildren method
     *
     * @param int $profile_id Profile id
     * @param int $union_id Union for which to return children. Return children from all unions on null.
     * @return array
     */
    private function __fetchChildren($profile_id, $union_id = null)
    {
        if (!empty($union_id) && !empty($this->_p2u[$profile_id][$union_id]['c'])) {
            return $this->_p2u[$profile_id][$union_id]['c'];
        } elseif (is_null($union_id) && !empty($this->_p2u[$profile_id])) {
            $children = [];
            foreach ($this->_p2u[$profile_id] as $union_id => $union) {
                if (!empty($union['c'])) {
                    $children += (array)$union['c'];
                }
            }

            return $children;
        }

        return [];
    }

    /**
     * fetchParents method
     *
     * @param int $profile_id Profile id
     * @access private
     * @return bool|array
     */
    private function __fetchParents($profile_id)
    {
        if (isset($this->_c2u[$profile_id])) {
            $union = reset($this->_c2u[$profile_id]);
            if (!empty($union['p'])) {
                $father = false;
                $mother = false;
                if ($this->_g[$union['p'][0]] == 'm') {
                    $father = $union['p'][0];
                    if (!empty($union['p'][1])) {
                        $mother = $union['p'][1];
                    }
                } else {
                    $mother = $union['p'][0];
                    if (!empty($union['p'][1])) {
                        $father = $union['p'][1];
                    }
                }
                if (!$father && !$mother) {
                    return false;
                }

                return ['dad' => $father, 'mom' => $mother];
            }
        }

        return false;
    }

    /**
     * __fetchSiblings method
     *
     * @param int $profile_id Profile id
     * @param bool $include_self Include root profile
     * @access public
     * @return array
     */
    private function __fetchSiblings($profile_id, $include_self = true)
    {
        if (isset($this->_c2u[$profile_id])) {
            $union = reset($this->_c2u[$profile_id]);

            if (!$include_self) {
                return array_diff($union['c'], [$profile_id]);
            } else {
                return $union['c'];
            }
        }

        return [];
    }

    /**
     * __bulkReadProfiles method
     *
     * Read specified profiles in $profile_list into $profiles variable
     *
     * @param mixed $profiles Profiles
     * @param mixed $profile_list Profile id array
     * @access private
     * @return void
     */
    private function __bulkReadProfiles(&$profiles, $profile_list)
    {
        if (empty($profile_list)) {
            return;
        }
        if (is_array($profile_list)) {
            $bulk = TableRegistry::get('Profiles')->find()
                ->select()
                ->where(['Profiles.id IN' => $profile_list])
                ->all();

            foreach ($bulk as $profile) {
                $profiles[$profile->id]['Profile'] = array_merge($profiles[$profile->id]['Profile'], $profile->toArray());
                $this->__cleanupProfile($profiles[$profile->id]['Profile']);
            }
        } elseif (is_numeric($profile_list) && !isset($profiles[$profile_list])) {
            $profiles[$profile_list] = TableRegistry::get('Profiles')->get($profile_list)->toArray();
            $this->__cleanupProfile($profiles[$profile_list]['Profile']);
        }
    }

    /**
     * __cleanupProfile method
     *
     * Remove unneeded fileds form profile
     *
     * @param mixed $profile Profile array
     * @access private
     * @return void
     */
    private function __cleanupProfile(&$profile)
    {
        if (empty($profile['dod_c'])) {
            unset($profile['dod_c']);
        }
        if (empty($profile['dob_c'])) {
            unset($profile['dob_c']);
        }

        if ($profile['ln'] == $profile['mdn']) {
            unset($profile['mdn']);
        }
    }

    /**
     * __addGhost method
     *
     * Creates ghost node and returns its id
     *
     * @param array $profiles Method to be called when user clicks ob ghost (add_parent, add_partner...)
     * @param int $x Node id to be passed as parameter to add method
     * @param int $y Array of profiles to which ghost will be added
     * @param array $options Y position of ghost node
     * @return int Ghost id
     */
    private function __addGhost(&$profiles, $x = 0, $y = 0, $options = [])
    {
        $ghost_id = $this->ghost_id;
        $this->ghost_id--;

        $profiles[(string)$ghost_id]['Profile'] = [
            'nt' => 'ghost',
            'id' => $ghost_id,
            'x' => $x,
            'y' => $y,
            'method' => isset($options['method']) ? $options['method'] : null,
            'ref' => isset($options['ref']) ? $options['ref'] : null
        ];
        if (!empty($options['direct_relative'])) {
            $profiles[$ghost_id]['Profile']['d_r'] = true;
        }

        return $ghost_id;
    }
}
