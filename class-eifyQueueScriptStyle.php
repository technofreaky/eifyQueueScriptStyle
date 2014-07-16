<?php
/**
* The eify_QueueScriptStyles is based on wordpress wp_enqueue_script / wp_enqueue_style
*
* @version 0.0.1
* @copyright 2014 - 2014
* @author Varun Sridharan (email: varunsridharan23@gmail.com)
* @link http://varunsridharan.in
*
* @license GNU General Public LIcense v3.0 - license.txt
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
* @package eify_QueueScriptStyles
*/

class eify_QueueScriptStyles {
	
	protected $style =  array();
	protected $script = array();
        protected $generated = array();
        protected $depended = array();
	
	/**
	 * Enqueue A Script / Style Based On The Input Given
	 * @param string $type
	 * @param string $handle
	 * @param string $src
	 * @param string | int  $version
	 * @param array $page
	 * @param array $attr
	 * @param boolean $footer
	 * @return boolean
	 */
	private function enqueue($type,$handle,$src,$version,$dependency= '',$page = '',$attr = '',$footer = false) {
		if(!empty($src)) {
			if(!array_key_exists($handle, $this->{$type})) {
				$this->{$type}[$handle] = array();
				$this->{$type}[$handle] = array();
				$this->{$type}[$handle]['handler'] = $handle;
				$this->{$type}[$handle]['src'] = $src;
				$this->{$type}[$handle]['version'] = $version;
				$this->{$type}[$handle]['dependency'] = $dependency;
				if(!empty($page) && is_array($page)) { $this->{$type}[$handle]['page'] = $page; }
		
				if(!empty($attr) && is_array($attr)) { $this->{$type}[$handle]['attr'] = $attr; }				
				
				$this->{$type}[$handle]['is_footer'] = $footer;
				 
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Check For Existing In Style / Script
	 * @param string $type
	 * @param string $handler
	 * @return array | boolean
	 */
	private function has_enqueue($type,$handler) {
		if(!empty($handler)) {
			if(array_key_exists($handler, $this->{$type})) {
				return $this->{$type}[$handler];
			} else { return false; }
		} else {
			return false;
		}
	}
	
	/**
	 * Removes A Script or Style From Queue
	 * @param string $type
	 * @param string $handler
	 * @return boolean
	 */
	private function dequeue($type,$handler){
		if(!empty($handler)) {
			if(array_key_exists($handler, $this->{$type})) {
				unset($this->{$type}[$handler]);
				return true;
			} else { return false; }
		} else {
			return false;
		}		
	}

    
    /**
     * Generates Attributes if avaiable
     */ 
    private function generateAttr($attrs){ 
        
        $attr = '';
        $attrs = $attrs;
        
        if(!empty($attrs)){
            foreach($attrs as $atKey => $atVal) {
                $attr .= $atKey.'="'.$atVal.'" ';
            } 
            return $attr;
        }
    }

    /*
     * Generates Tag For The Given Values
     */
    private function generateTag($type,$footer,$val){ 
        if(isset($val['page']) && is_array($val['page'])){
            if(!in_array($currPage, $val['page'])) {
                continue;
            }
        } else if(isset($val['page']) && is_string($val['page'])){
            if($currPage !== $val['page']) {
                continue;
            }
        }
        if($footer == $val['is_footer']) {
            $tag = '';
            $attr = '';
            if(isset($val['attr'])){
                $attr = $this->generateAttr($val['attr']);
            }

            if($type == 'style') {
                $tag = '<link  rel="stylesheet" href="'.$val['src'].'"?v='.$val['version'].' '.$attr.' />'.PHP_EOL;
            } else if ($type = 'script') {
                $tag = '<script src="'.$val['src'].'"?v='.$val['version'];
                $tag .= ' type="text/javascript" '.$attr.'> </script>'.PHP_EOL ;
            }
            return $tag;
        }        
    }

    /**
     * Checks For Dependency
     */ 
    private function checkDepn($val){ 
        if(isset($val['dependency']) && !empty($val['dependency'])) {
            if(! in_array($val['dependency'], $this->generated)){ 
                $tempArr = array();
                $tempArr['handler'] = $val['handler'];
                $tempArr['dependency'] = $val['dependency'];
                if(!isset($this->depended[$val['handler']]))
                    $this->depended[$val['handler']] = $tempArr;
                    $generate = false;
            } else {  
                unset($this->depended[$val['handler']]);
                $generate = true;
            }
        } else { 
            
            $generate = true;
        }        

        return $generate;
    }
    
    /**
     * Checks for dependency to load
     */
    private function checkLoad($type,$footer){
        $data = $this->depended;
        $type = $type;
        $tags = '';
        foreach($data as $key => $value) {  
            $val = $this->{$type}[$value['handler']];
            if($this->checkDepn($val)) {  
                unset($this->depended[$key]);
                $tags .= $this->generateTag($type,$footer,$this->{$type}[$value['handler']]);
                $this->generated[] = $value['handler'];
            } else { 
               continue;
            }
        }
        return $tags;
    }
    
    
	/**
	 * Generate HTML Output for Script / Style
	 * @param string $type
	 * @param string $footer
	 * @return string|boolean
	 */
	public function generate($type,$footer = false) {
		$data = $this->{$type};
		$generated_{$type} = '';
        
	 	if(!empty($data)) {
			$currPage = '';

			foreach($data as $key => $val) {  
                
                $generate = true;
                $generate = $this->checkDepn($val);
                
				if($generate){
				    $generated_{$type} .= $this->generateTag($type,$footer,$val);
					$this->generated[] = $val['handler'];
                    $check = $this->checkLoad($type,$footer);
                    if($check) { 
                       $generated_{$type} .= $check;
                    }                    
                }
            }
                    
            $reCount = count($this->depended);
            $i = 0;
            while($i<$reCount) {
                $check = $this->checkLoad($type,$footer);
                if($check) { 
                   $generated_{$type} .= $check;
                }
                $i++;
            }
            return $generated_{$type};
		}
		return false;
	}

    
	/**
	 * Enqueue A Script to Queue
	 * @param string $type
	 * @param string $handle
	 * @param string $src
	 * @param string | int  $version
	 * @param array $page
	 * @param array $attr
	 * @param boolean $footer
	 * @return boolean
	 */
    
	public function script_enqueue($handle,$src,$version,$dependency= '',$page = '',$attr = '',$footer = false) {
		return $this->enqueue('script',$handle,$src,$version,$dependency,$page,$attr,$footer);
	}

	/**
	 * Enqueue A Style to Queue
	 * @param string $type
	 * @param string $handle
	 * @param string $src
	 * @param string | int  $version
	 * @param array $page
	 * @param array $attr
	 * @param boolean $footer
	 * @return boolean
	 */
	public function style_enqueue($handle,$src,$version,$dependency= '',$page = '',$attr = '',$footer = false) { 
		return $this->enqueue('style',$handle,$src,$version,$dependency,$page,$attr,$footer);	
	}	
	
	/**
	 * Checks For Existing Script In Queue
	 * @param string $handler
	 * @return boolean | Array
	 */
	public function has_script($handler){ 
		return $this->has_enqueue('script',$handler); 
	}
	
	/**
	 * Checks For Existing Style In Queue
	 * @param string $handler
	 * @return boolean | Array
	 */	
	public function has_style($handler){ 
		return $this->has_enqueue('style',$handler); 
	}	
	
	/**
	 * Removes A Script if Existing In Queue
	 * @param string $handler
	 * @return boolean | Array
	 */	
	public function dequeue_script($handler){ 
		return $this->dequeue('script',$handler); 
	}
	
	/**
	 * Removes A Style if Existing In Queue
	 * @param string $handler
	 * @return boolean | Array
	 */	
	public function dequeue_style($handler){ 
		return $this->dequeue('style',$handler); 
	}		

	/**
	 * Generates HTML For All Script In Queue 
	 * @param string $handler
	 * @return string | boolean
	 */
	public function get_script($footer = false) {
		return $this->generate('script',$footer); 
	}

	/**
	 * Generates HTML For All Style In Queue
	 * @param string $handler
	 * @return string | boolean
	 */
	public function get_style($footer = false) { 
		return $this->generate('style',$footer); 
	}	
	
}
?>
