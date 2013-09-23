<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The class handles all requests about routine.
 * @author: Xie Haozhe <zjhzxhz@gmail.com>
 */
class Lib_scores {
    /**
     * @var A instance of CodeIgniter.
     */
    private $__CI;
    
    /**
     * The contructor of the Routine Library class.
     */
    public function __construct() 
    {
        $this->__CI =& get_instance();
        $this->__CI->load->model('Students_model');
        $this->__CI->load->model('Scores_model');

        $this->__CI->load->library('lib_utils');
    }

    /**
     * Import scores from an excel file.
     * @param  String $file_path - the path of the excel file
     * @param  Array  $result - an array contains query flags.
     * @return true if query is successful
     */
    public function import_scores($file_path, &$result)
    {
        $this->__CI->load->library('lib_excel');
        $data = $this->__CI->lib_excel->get_data_from_excel($file_path);

        $number_of_records = count($data);
        $result['is_query_successful'] = true;
        for ( $i = 1; $i < $number_of_records; ++ $i ) {
            $score = $this->get_scores_array($data[$i]);
            if ( $score['course_type'] != 1 ) {
                continue;
            }
            $query_result = $this->import_score_record($score);
            $result['is_query_successful']  &= $query_result['is_successful'];
            if ( $query_result['is_successful'] ) {
                $result['success_message']  .= $score['student_name'].'的'.$score['course_name'].'成绩信息已成功导入.<br />';
            } else {
                $result['success_message']  .= $score['student_name'].'的'.$score['course_name'].'成绩信息未能成功导入.<br />';
            }
        }
        return $result['is_query_successful'];
    }

    /**
     * [import_score_record description]
     * @param  [type] $score_record [description]
     * @return [type]               [description]
     */
    private function import_score_record(&$score_record)
    {
        $result = array(
                'is_successful' => false
            );
        $score  = array(
                'year'          => $score_record['year'],
                'semester'      => $score_record['semester'],
                'student_id'    => $score_record['student_id'],
                'course_id'     => $score_record['course_id'],
                'paper_score'   => $score_record['paper_score'],
                'final_score'   => $score_record['final_score'], 
                'is_hierarchy'  => $score_record['is_hierarchy'], 
                'is_passed'     => $score_record['is_passed'] 
            );
        $result['is_successful'] = $this->__CI->Scores_model->insert($score);

        return $result;
    }

    /**
     * Get information of the user which read from an excel file to 
     * a row array.
     * @param  Array $record - an array contains user's information
     * @return an array which contains the information of the user
     */
    private function get_scores_array(&$record)
    {
        $final_score            =  $this->get_score($record[15]);
        $score = array(
                'year'          => (int)((int)$record[0] / 2 + 2002),
                'semester'      => ((int)$record[0] % 2 != 0 ? 1 : 2),
                'student_id'    => (string)$record[1],
                'student_name'  => (string)$record[2],
                'course_id'     => (string)$record[4],
                'course_name'   => (string)$record[5],
                'course_type'   => (int)$record[7],
                'paper_score'   => $this->get_score($record[13]),
                'final_score'   => $final_score,
                'is_hierarchy'  => ( (string)$record[15][0] == 'Z' ? true : false ),
                'is_passed'     => ( $final_score >= 60 ? true : ( (string)$record[17] == 'Y11' ? true :  false) )
            );
        return $score;
    }

    /**
     * Convert score from hundred mark system into hierarchy.
     * @param  [type] $score [description]
     * @return [type]        [description]
     */
    private function get_score($score)
    {
        if ( is_numeric($score) ) {
            if ( $score > 100 ) {
                return 100;
            } else {
                return $score;
            }
        } else {
            if ( $score == 'Z11' ) {
                return 95;
            } else if ( $score == 'Z12' ) {
                return 85;
            } else if ( $score == 'Z13' ) {
                return 70;
            } else if ( $score == 'Z14' ) {
                return 60;
            } else {
                return 0;
            }
        }
    }

    private function get_rank($score)
    {

    }

    /**
     * Get the grade point of a certain grade.
     * @param  int     $score        [description]
     * @param  boolean $is_hierarchy [description]
     * @return the grade point of the grade
     */
    private function get_grade_point($score, $is_hierarchy, $is_passed)
    {
        if ( $is_hierarchy ) {
            if ( $score == 95 ) {
                return 3.9;
            } else if ( $score == 85 ) {
                return 3.0;
            } else if ( $score == 70 ) {
                return 2.0;
            } else if ( $score == 60 ) {
                return 1.2;
            } else {
                if ( $is_passed ) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            if ( $score >= 95 ) {
                return 4.3;
            } else if ( $score >= 90 && $score < 95 ) {
                return 4;
            } else if ( $score >= 85 && $score < 90 ) {
                return 3.7;
            } else if ( $score >= 82 && $score < 85 ) {
                return 3.3;
            } else if ( $score >= 78 && $score < 82 ) {
                return 3;
            } else if ( $score >= 75 && $score < 78 ) {
                return 2.7;
            } else if ( $score >= 72 && $score < 75 ) {
                return 2.3;
            } else if ( $score >= 68 && $score < 72 ) {
                return 2;
            } else if ( $score >= 66 && $score < 68 ) {
                return 1.7;
            } else if ( $score >= 64 && $score < 66 ) {
                return 1.3;
            } else if ( $score >= 60 && $score < 64 ) {
                return 1;
            } else {
                if ( $is_passed ) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }
    }
}

/* End of file lib_scores.php */
/* Location: ./application/libraries/lib_scores.php */
