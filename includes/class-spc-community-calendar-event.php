<?php

use Spatie\CalendarLinks\Link;

/**
 * This class represents single event
 *
 * @since      1.0.0
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes
 * @author     Darko Gjorgjijoski <dg@darkog.com>
 */
class SPC_Community_Calendar_Event
{

    /**
     * The event
     * @var WP_Post|null
     */
    private $event = null;

    /**
     * The event meta
     * @var null
     */
    private $meta = array();

    /**
     * SPCC_Event constructor.
     *
     * @param $event
     */
    public function __construct($event)
    {

        $this->event = $event;

        $this->setup_meta();
    }

    public function get_id()
    {
        return $this->event['ID'];
    }

    /**
     * Return title
     * @return string
     */
    public function get_title()
    {
        return $this->event['post_title'];
    }

    /**
     * Is featured?
     * @return int
     */
    public function is_featured()
    {
        return (int)$this->get_meta_value('event_featured', 0);
    }

    /**
     * The event excerpt
     * @return string
     */
    public function get_excerpt()
    {

        $excerpt = trim($this->event['post_excerpt']);

        if (empty($excerpt)) {
            $excerpt = $this->event['post_content'];
            $excerpt = wp_trim_words($excerpt, 30);
        }

        return $excerpt;
    }

    /**
     * Return thumbnail
     *
     * @param $size
     *
     * @return string
     */
    public function get_thumbnail($size)
    {
        return isset($this->event['image'][$size]) ? $this->event['image'][$size] : '';
    }

    /**
     * Return address
     * @return mixed|string
     */
    public function get_address()
    {
        return $this->get_meta_value('event_address');
    }

    /**
     * Return address 2
     * @return mixed|string
     */
    public function get_address2()
    {
        return $this->get_meta_value('event_address2');
    }

    /**
     * Return address
     * @return mixed|string
     */
    public function get_city()
    {
        return $this->get_meta_value('event_city');
    }

    /**
     * Return address
     * @return mixed|string
     */
    public function get_state()
    {
        return $this->get_meta_value('event_state');
    }

    /**
     * Return address
     * @return mixed|string
     */
    public function get_postal_code()
    {
        return $this->get_meta_value('event_postal_code');
    }

    /**
     * Event cost
     * @return mixed|string
     */
    public function get_cost()
    {
        return $this->get_meta_value('event_cost');
    }

    /**
     * Add tickets url
     */
    public function get_tickets_url()
    {
        return $this->get_meta_value('event_tickets_url');
    }

    /**
     * Returns website
     * @return mixed|string
     */
    public function get_website()
    {
        $website = $this->get_meta_value('event_website');

        if (empty($website)) {
            return '';
        }

        #$website = str_replace( 'http://', 'https://', $website );
        #if ( strpos( 'https://', $website ) === false ) {
        #	$website = 'https://' . $website;
        #}

        return $website;
    }

    /**
     * Is virtual?
     * @return bool
     */
    public function is_virtual()
    {
        return $this->get_meta_value('event_attendance', 'physical') === 'virtual';
    }

    /**
     * Is physical?
     * @return bool
     */
    public function is_physical()
    {
        return $this->get_meta_value('event_attendance', 'physical') === 'virtual';
    }

    /**
     * Returns the formatted address.
     */
    public function get_formatted_address()
    {
        $parts = array();
        foreach (
            array(
                'event_address',
                'event_address2',
                'event_city',
                'event_state',
                'event_postal_code'
            ) as $part
        ) {
            $value = $this->get_meta_value($part);
            if (!empty($value)) {
                array_push($parts, $value);
            }
        }

        if (!empty($parts)) {
            return implode(', ', $parts);
        } else {
            return '';
        }
    }

    /**
     * Return the formatted datetime.
     * @return string
     */
    public function get_formatted_datetime()
    {
        $date_start = $this->get_start_date('M d @ h:i A');


        $date_end = $this->get_end_date('M d @ h:i A');

        if ($date_start === $date_end) {
            $date = $date_start;
        } else {
            if (!empty($date_start) && !empty($date_end)) {
                $date = $date_start . ' - ' . $date_end;
            } else if (!empty($date_start)) {
                $date = $date_start;
            } else {
                $date = '';
            }
        }

        return $date;
    }

    /**
     * Returns the formatted date.
     * @return string
     */
    public function get_formatted_date()
    {
        $date_start = $this->get_start_date('M d, Y');
        $date_end = $this->get_end_date('M d, Y');

        if ($date_start === $date_end) {
            $date = $date_start;
        } else {
            if (!empty($date_start) && !empty($date_end)) {
                $date = $date_start . ' - ' . $date_end;
            } else if (!empty($date_start)) {
                $date = $date_start;
            } else {
                $date = '';
            }
        }

        return $date;
    }


    /**
     * Returns start date
     *
     * @param null $format
     *
     * @return string
     */
    public function get_start_date($format = 'M d, Y h:i A')
    {
        $start_date = $this->get_meta_value('event_start');

        return $this->format_date($start_date, $format);
    }

    /**
     * Returns end date
     *
     * @param null $format
     *
     * @return string
     */
    public function get_end_date($format = 'M d, Y h:i A')
    {
        $start_date = $this->get_meta_value('event_end');

        return $this->format_date($start_date, $format);
    }

    /**
     * Returns formatted event date.
     */
    public function get_formatted_event_date()
    {

        $event_start_meta = $this->get_meta_value('event_start');
        $event_end_meta = $this->get_meta_value('event_end');
        return self::format_theme_date($event_start_meta, $event_end_meta);

    }


    public static function format_theme_date($event_start_meta, $event_end_meta)
    {
        $event_start = '';
        $event_end = '';

        if (!empty($event_start_meta)) {
            $event_start_dt = DateTime::createFromFormat('Y-m-d H:i:s', $event_start_meta);
            $event_start = $event_start_dt->format('M') . ' ' . $event_start_dt->format('d');
            $event_start = $event_start . ' @ ' . $event_start_dt->format('g:i A');
        }

        if (!empty($event_end_meta)) {
            $event_end_dt = DateTime::createFromFormat('Y-m-d H:i:s', $event_end_meta);
            $event_end = $event_end_dt->format('M') . ' ' . $event_end_dt->format('d');
            $event_end = $event_end . ' @ ' . $event_end_dt->format('g:i A');
        }

        $dates = array();
        if (!empty($event_start)) {
            $dates[] = $event_start;
        }
        if (!empty($event_end)) {
            $dates[] = $event_end;
        }

        return implode(' - ', $dates);
    }


    /**
     * Return the single event link
     * @return false|string
     */
    public function get_link()
    {
        $settings = new SPC_Community_Calendar_Settings();
        $events_page_ID = $settings->get('events_page');
        $base = get_permalink($events_page_ID);
        if (empty($base)) {
            return '/404';
        }
        $url = trailingslashit($base) . $this->event['post_name'];

        return $url;
    }

    /**
     * Return address
     * @return mixed|string
     */
    public function get_country()
    {
        return $this->get_meta_value('event_country');
    }

    /**
     * Return lat
     * @return mixed|string
     */
    public function get_lat()
    {
        return $this->get_meta_value('event_lat');
    }

    /**
     * Return lng
     * @return mixed|string
     */
    public function get_lng()
    {
        return $this->get_meta_value('event_lng');
    }

    /**
     * Returns venue
     * @return string
     */
    public function get_venue()
    {
        return $this->get_meta_value('event_venue');
    }

    /**
     * Returns phone
     * @return string
     */
    public function get_phone()
    {
        return $this->get_meta_value('event_phone');
    }

    /**
     * Returns phone
     * @return string
     */
    public function get_email()
    {
        return $this->get_meta_value('event_email');
    }

    /**
     * The event host
     * @return mixed|string
     */
    public function get_parking()
    {
        return $this->get_meta_value('event_parking');
    }

    /**
     * The event host
     * @return mixed|string
     */
    public function get_organizer()
    {
        return $this->get_meta_value('event_host');
    }

    /**
     * The categories
     * @return array
     */
    public function get_categories()
    {
        return isset($this->event['categories']) ? (array)$this->event['categories'] : array();
    }

    /**
     * The categories
     * @return array
     */
    public function get_tags()
    {
        return isset($this->event['tags']) ? (array)$this->event['tags'] : array();
    }

    /**
     * The event content
     * @return string
     */
    public function get_event_content()
    {
        return $this->event['post_content'];
    }

    /**
     * Return calendar links
     * @return array
     */
    public function get_calendar_links()
    {

        $start = $this->get_meta_value('event_start');
        $end = $this->get_meta_value('event_end');

        if (strtotime($start) <= strtotime($end)) {
            $event_start = $start;
            $event_end = $end;
        } else {
            $event_start = $end;
            $event_end = $start;
        }

        $links = array();

        if (empty($event_start) || empty($event_end)) {
            return $links;
        }

        $from = DateTime::createFromFormat('Y-m-d H:i:s', $event_start);
        $to = DateTime::createFromFormat('Y-m-d H:i:s', $event_end);

        $options = array('google' => 'Google', 'yahoo' => 'Yahoo', 'webOutlook' => 'Outlook', 'ics' => 'Apple iCal');


        $categories = $this->get_categories();
        $description = array();
        foreach ($categories as $category) {
            array_push($description, $category->name);
        }
        $description = !empty($description) ? implode(', ', $description) : '';

        try {
            $link = Link::create($this->get_title(), $from, $to)
                ->description($description)
                ->address($this->get_formatted_address());
        } catch (\Exception $e) {
            return array();
        }

        $data = array();

        foreach ($options as $method => $name) {
            if (method_exists($link, $method)) {
                $data[$name] = $link->$method();
            }
        }

        return $data;
    }

    /**
     * Return the share icons
     */
    public function get_share_icons()
    {
        $icons = new SPC_Community_Calendar_Shareicons();
        $args = array(
            'url' => $this->get_link(),
            'title' => $this->get_title(),
        );
        $generated = $icons->generate($args);

        return $generated;
    }

    /**
     * Print the share icons
     * @return void
     */
    public function print_share_icons()
    {
        $generated = $this->get_share_icons();
        if (empty($generated)) {
            echo '';
        }
        $markup = '<ul class="spc-share-icons">';
        foreach ($generated as $key => $value) {
            $title = sprintf('Share on %s', ucfirst($key));
            $markup .= '<li class="spc-share-icons--icon spc-share-icons--icon--'.$key.'"><a title="'.$title.'" href="' . $value . '"><span class="spcc-icon-' . $key . '"></span></a></li>';
        }
        $markup .= '</ul>';
        echo $markup;
    }

    /**
     * Return the google calendar url formatted,
     *
     * /// COPY TO CALENDAR
     * /// https://calendar.google.com/calendar/r/eventedit?text=Test&dates=20191030T183000/20191030T210000&details=Overview%0AWe%26%238217;re+a+group+of+women+who+want+to+learn+JavaScript+together.+We+welcome+people+of+all+levels.+Beginners+are+especially+encouraged.+You+move+at+your+own+pace+in+this+group,+so+it+doesn%26%238217;t+matter+if+you+are+a+fast+or+slow+learner,+or+if+you+attend+every+week.+(You+can+use+any+study+materials+or+work+on+personal+side+projects.+Whatever+you+prefer+to+learn.)+%0AWe+meet+every+Wednesday,+6:30+%26%238211;+8:30pm.+%0AFormat%0AWe+generally+start+introductions+around+6:45pm+and+then+work+on+coding+problems,+independently+or+with+others.+Many+of+us+are+using+codecademy.com+(http://codecademy.com/),+but+we+welcome+other+tools.+Although+the+focus+is+on+JavaScript,+we+also+cover+closely+related+topics+such+as+HTML+and+CSS.+%0AThere+will+typically+be+more+experienced+coders+available+to+help+beginners+with+what+they+are+stuck+on.+%0A(The+only+tools+you+need+to+start+using+JavaScript+are+a+web+browser+and+optionally+a+simple+text+editor+or+code+editor.)+%0AThis+event+is+targeted+to+people+who+(View+Full+Event+Description+Here:+https://wpshindig.com/event/womens-javascript-study-group/2019-10-30/)&location=Mavenlink,+23+Geary+St+%23500,+San+Francisco,+CA,+94102,+United+States&trp=false&sprop=website:https://wpshindig.com&ctz=America/Los_Angeles&sf=true
     **
     * @return string
     */
    public function get_google_calendar_url()
    {

        $event_title = $this->event['post_title'];

        // eg.1 20190822T113000
        // eg.2 20191030T183000
        //$event_start_date = tb_get_event_date_in_format( $event_id, 'Ymd\THis' );
        //$event_end_date   = tb_get_event_end_date_in_format( $event_id, 'Ymd\THis' );
        //$event_address = get_post_meta( $event_product_id, 'WooCommerceEventsLocation', true );

        $dates = array();
        $address = array();

        $event_start = $this->get_meta_value('event_start');
        $event_end = $this->get_meta_value('event_end');

        if (!empty($event_start)) {
            $event_start = $this->format_date($event_start, 'Ymd\THis');
            array_push($dates, $event_start);
        }
        if (!empty($event_end)) {
            $event_end = $this->format_date($event_end, 'Ymd\THis');
            array_push($dates, $event_end);
        }

        $address_parts = array(
            'event_venue',
            'event_address',
            'event_city',
            'event_state',
            'event_postal_code',
            'event_country'
        );

        foreach ($address_parts as $part) {
            $event_meta_part = $this->get_meta_value($part);
            if (!empty($event_meta_part)) {
                array_push($address, $event_meta_part);
            }
        }

        $params = array(
            'text' => $event_title,
            'location' => implode(' ', $address),
            'website' => $this->get_link(),
            'ctz' => 'America/New_York',
        );

        if (count($dates) === 2) {
            $params['dates'] = implode('/', $dates);
        } else if (count($dates) === 1) {
            $params['date'] = $dates[0];
        }

        $query = http_build_query($params);

        return "https://calendar.google.com/calendar/r/eventedit?{$query}";
    }

    /**
     * @param $key
     * @param string $default
     *
     * @return mixed|string
     */
    private function get_meta_value($key, $default = '')
    {
        return isset($this->meta[$key]) ? $this->meta[$key] : $default;
    }

    /**
     * Format date
     *
     * @param $date (Y-m-d H:i:s)
     * @param null $format
     *
     * @return string
     */
    private function format_date($date, $format = null)
    {

        if (empty($date) || is_null($format)) {
            return $date;
        }
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $date);

        return $dt->format($format);

    }

    /**
     * Setup the metadata
     */
    private function setup_meta()
    {

        $this->meta = isset($this->event['meta']) ? $this->event['meta'] : array();
    }


    /**
     * To array!
     * @return null
     */
    public function to_array()
    {

        $data = $this->meta;
        $data['event_id'] = $this->get_id();
        $data['event_title'] = $this->get_title();
        $data['event_address_formatted'] = $this->get_formatted_address();
        $data['event_start_date_formatted'] = $this->get_start_date();
        $data['event_end_date_formatted'] = $this->get_end_date();

        return $data;
    }


    /**
     * Return the social urls
     * @return array
     */
    public function get_social_urls()
    {
        $urls = array();
        foreach (array('facebook', 'twitter', 'linkedin', 'pinterest') as $key) {
            $value = $this->get_meta_value('event_' . $key, '');
            if (empty($value)) {
                continue;
            }
            $urls[$key] = $value;
        }

        return $urls;
    }

    public function the_thumbnail($size = 'large')
    {
        $image = isset($this->event['image'][$size]) ? $this->event['image'][$size] : null;
        if (empty($image)) {
            return;
        }
        echo '<img src="' . $image . '">';
    }

}