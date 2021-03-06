<?php

namespace Cd\Http\Controllers;

use Illuminate\Http\Request;

use Cd\Http\Requests;

use Cd\Opportunity;

use Cd\Drupal;

use stdClass;

class OpportunityController extends Controller
{
    private $user = false;

    /**
     * Sets current user.
     */
    public function __construct(){
        $session = false;
        $session = drupal_session()['id'];
        if( $session ){
            $this->user = Drupal::table('sessions')
                ->leftJoin('profile', 'sessions.uid', '=', 'profile.uid')
                ->where('sessions.sid', $session)
                ->select('uuid')
                ->first();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $limit = isset( $_GET['limit'] ) ? (int)$_GET['limit'] : 10;
        $offset = isset( $_GET['offset'] ) ? (int)$_GET['offset'] : 0;
        $sort = 'created_at';
        $order = 'asc';

        if( isset( $_GET['sort'] ) ){
            $sign = substr( $_GET['sort'], 0, 1 );
            $sstr = substr( $_GET['sort'], 1 );
            if( $sign === '-' ){
                $sort = $sstr;
                $order = 'desc';
            }else{
                $sort = ( $sign === '+' ) ? $sstr : $_GET['sort'];
                $order = 'asc';
            }
        }

        $opps = Opportunity::orderBy( $sort, $order )
            ->take( $limit )
            ->skip( $limit * $offset )
            ->get();

        if( isset( $_GET['fields'] ) ){
            $fields = explode( ',', $_GET['fields'] );
            $query = Drupal::table('profile');
            foreach ($fields as $field) {
                $query->leftJoin( $field, 'profile.pid', '=', $field . '.entity_id' );
            }
        }

        return $opps;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $opp = new Opportunity;
        if( ! is_null( $request->title ) )
            $opp->title = $request->title;
        if( ! is_null( $request->logo ) )
            $opp->logo = $request->logo;

        $opp->author = (object)['data'=>[(object)['id'=>$this->user->uuid]]];
        if( $opp->save() )
            return 1;
        return 0;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $opp = Opportunity::find($id);

        $wrtrs = $opp->author['data'];
        $writers = array_map( [$this, 'get_author_ids'], $wrtrs);

        $fields = [
            'field_data_field_profile_bi_user_name',
            'field_data_field_profile_cover',
            'field_data_field_profile_picture'
            ];

        $query = Drupal::table('profile');

        foreach ($fields as $field) {
            $query->leftJoin( $field, 'profile.pid', '=', $field . '.entity_id' );
        }

        $profiles = $query->whereIn('profile.uuid', $writers)->get();
        $authors = [];

        foreach ($profiles as $profile) {
            $author = [
                'id' => $profile->uuid,
                'href' => url( '/v1/profiles/' . $profile->uuid ),
                'name' => $profile->field_profile_bi_user_name_value
            ];
            $authors[] = $author;
        }

        $opp->author = (object)[
            'href' => url( '/v1/opprtunities/' . $id . '/author' ),
            'data' => $authors
        ];

        return $opp;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $opp = Opportunity::find($id);
        if( ! is_null( $request->title ) )
            $opp->title = $request->title;
        if( ! is_null( $request->logo ) )
            $opp->logo = $request->logo;
        if( $opp->save() )
            return 1;
        return 0;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if( Opportunity::destroy($id) )
            return 1;
        return 0;
    }

    protected function get_author_ids($auth){
        return $auth['id'];
    }
}
